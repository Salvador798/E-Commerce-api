<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Cart;
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
            $cart = Cart::with('items.product.inventory')
                ->where('user_id', $userId)
                ->first();

            if (!$cart || $cart->items->isEmpty()) {
                throw new Exception('The cart is empty');
            }

            // Verify that the address belongs to the user
            $address = Address::where('id', $data['address_id'])
                ->where('user_id', $userId)
                ->first();

            if (!$address) {
                throw new Exception('The address does not belong to the user');
            }

            // Calculate total
            $total = 0;
            foreach ($cart->items as $item) {
                $total += $item->product->price * $item->quantity;
            }

            // Create order
            $order = Order::create([
                'user_id' => $userId,
                'address_id' => $data['address_id'],
                'date' => now(),
                'status' => 'pendiente',
                'total' => $total
            ]);

            // Create items and deduct inventory
            foreach ($cart->items as $item) {

                if ($item->product->inventory->available_quantity < $item->quantity) {
                    throw new Exception("Stock insuficiente para {$item->product->name}");
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product->id,
                    'quantity' => $item->quantity,
                    'emit_price' => $item->product->price
                ]);

                // Discount stock
                $item->product->inventory->available_quantity -= $item->quantity;
                $item->product->inventory->save();
            }

            // Register payment
            $payment = Payment::create([
                'order_id' => $order->id,
                'date' => now(),
                'amount' => $total,
                'method' => $data['payment_method'],
                'status' => 'aprobado'
            ]);

            // Cambiar estado del pedido
            $order->status = 'pagado';
            $order->save();

            // Generate shipment
            $shipment = Shipment::create([
                'order_id' => $order->id,
                'carrier' => 'DHL',
                'tracking' => strtoupper(Str::random(12)),
                'estimated_date' => now()->addDays(3),
                'status' => 'en_transito'
            ]);

            // Change order status
            $order->status = 'enviado';
            $order->save();

            // Empty cart
            $cart->items()->delete();

            // Devolver todo el flujo
            return [
                'order' => $order->load('items', 'address'),
                'payment' => $payment,
                'shipment' => $shipment
            ];
        });
    }
}
