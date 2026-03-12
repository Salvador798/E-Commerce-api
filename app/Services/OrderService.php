<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Create a new order with validation and inventory management.
     *
     * This method performs the following operations within a database transaction:
     * 1. Validates that all products have sufficient stock
     * 2. Calculates the total order amount
     * 3. Creates the order record
     * 4. Creates order items for each product
     * 5. Decrements available inventory for each product
     *
     * @param array $data Order data containing:
     *                    - user_id: The ID of the user placing the order
     *                    - address_id: (optional) The ID of the delivery address
     *                    - items: Array of items, each with product_id and quantity
     * @return \App\Models\Order The created Order model with loaded relationships (items, address, payment, shipment)
     * @throws \Exception If any product has insufficient stock
     */
    public function create(array $data)
    {
        // Use a database transaction to ensure data consistency
        return DB::transaction(function () use ($data) {
            $total = 0;

            // First loop: calculate total order amount and validate stock availability
            foreach ($data['items'] as $item) {

                // Fetch the product from database
                $product = Product::findOrFail($item['product_id']);
                
                // Get the product's inventory record
                $inventory = $product->inventory;

                // Check if inventory exists and has enough stock
                if (!$inventory || $inventory->available_quantity < $item['quantity']) {
                    throw new Exception("Insufficient stock for {$product->name}");
                }

                // Add product price * quantity to total
                $total += $product->price * $item['quantity'];
            }

            // Create the order record in the database
            $order = Order::create([
                'user_id' => $data['user_id'],
                'address_id' => $data['address_id'] ?? null,
                'date' => now(),
                'status' => 'pendiente',
                'total' => $total
            ]);

            // Second loop: create order items and decrement inventory
            foreach ($data['items'] as $item) {

                // Fetch the product again (required for creating order item)
                $product = Product::findOrFail($item['product_id']);
                
                // Get the product's inventory record
                $inventory = $product->inventory;

                // Create the order item record with product details
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'emit_price' => $product->price
                ]);

                // Decrease the available quantity in inventory
                $inventory->available_quantity -= $item['quantity'];
                $inventory->save();
            }

            // Return the created order with all related data loaded
            return $order->load([
                'items.product',
                'address',
                'payment',
                'shipment'
            ]);
        });
    }
}
