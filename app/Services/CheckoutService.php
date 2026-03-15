<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Shipment;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutService
{
    public function processCheckout(array $data, int $userId)
    {
        return DB::transaction(function () use ($data, $userId) {

            // Get Cart
            $cart = Cart::with('items.product')
                ->where('user_id', $userId)
                ->first();

            if (!$cart || $cart->items->isEmpty()) {
                throw new Exception('The cart is empty');
            }

            // Verify address ownership
            $address = Address::where('id', $data['address_id'])
                ->where('user_id', $userId)
                ->first();

            if (!$address) {
                throw new Exception('The address does not belong to the user');
            }

            // Calculate total
            $total = $cart->items->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

            // Create order
            $order = Order::create([
                'user_id' => $userId,
                'address_id' => $data['address_id'],
                'date' => now(),
                'status' => 'pendiente',
                'total' => $total
            ]);

            // Registrar log del pedido
            UserLogService::add(
                'CREAR_PEDIDO',
                'PEDIDOS',
                "Pedido #{$order->id} creado con total {$order->total}",
                $userId
            );

            // Process items
            foreach ($cart->items as $item) {

                $inventory = Inventory::where('product_id', $item->product->id)
                    ->lockForUpdate()
                    ->first();

                if (!$inventory) {
                    throw new Exception("Inventory not found for {$item->product->name}");
                }

                if ($inventory->available_quantity < $item->quantity) {
                    throw new Exception("Stock insuficiente para {$item->product->name}");
                }

                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product->id,
                    'quantity' => $item->quantity,
                    'emit_price' => $item->product->price
                ]);

                // Deduct inventory
                $inventory->available_quantity -= $item->quantity;
                $inventory->save();
            }

            // Register payment
            $payment = Payment::create([
                'order_id' => $order->id,
                'date' => now(),
                'amount' => $total,
                'method' => $data['payment_method'],
                'status' => 'aprobado'
            ]);

            UserLogService::add(
                'CREAR_PAGO',
                'PAGOS',
                "Pago #{$payment->id} para pedido #{$order->id} por {$payment->amount} ({$payment->status})",
                $userId
            );

            // Update order status
            $order->update(['status' => 'pagado']);

            // Create shipment
            $shipment = Shipment::create([
                'order_id' => $order->id,
                'carrier' => 'DHL',
                'tracking' => strtoupper(Str::random(12)),
                'estimated_date' => now()->addDays(3),
                'status' => 'en_transito'
            ]);

            UserLogService::add(
                'CREAR_ENVIO',
                'ENVIOS',
                "Envio #{$shipment->id} para pedido #{$order->id} con {$shipment->carrier}",
                $userId
            );

            // Update order status again
            $order->update(['status' => 'enviado']);

            // Empty cart
            $cart->items()->delete();

            UserLogService::add(
                'VACIAR_CARRITO',
                'CARRITO',
                "Carrito #{$cart->id} vaciado luego del checkout del pedido #{$order->id}",
                $userId
            );

            return [
                'order' => $order->load('items.product', 'address'),
                'payment' => $payment,
                'shipment' => $shipment
            ];
        });
    }
}
