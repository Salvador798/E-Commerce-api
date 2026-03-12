<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Shipment;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShipmentService
{
    /**
     * Retrieve all shipment records with their associated orders.
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of Shipment models with loaded orders
     */
    public function all()
    {
        return Shipment::with('order')->get();
    }

    /**
     * Create a new shipment for an order with validation.
     *
     * This method performs the following operations within a database transaction:
     * 1. Validates that the order exists and has an address
     * 2. Checks if the order is paid (status must be 'pagado')
     * 3. Verifies that the order doesn't already have a shipment
     * 4. Creates the shipment record with auto-generated tracking number if not provided
     * 5. Updates the order status to 'enviado' if shipment is in transit
     *
     * @param array $data Shipment data containing:
     *                    - order_id: The ID of the order to ship
     *                    - carrier: The shipping carrier name
     *                    - tracking: (optional) Tracking number, auto-generated if not provided
     *                    - estimated_date: Estimated delivery date
     * @return \App\Models\Shipment The created Shipment model with loaded relationships
     * @throws \Exception If order is not paid, already has shipment, or lacks address
     */
    public function create(array $data)
    {
        // Use a database transaction to ensure data consistency
        return DB::transaction(function () use ($data) {

            // Fetch the order with its address from the database
            $order = Order::with('address')->findOrFail($data['order_id']);

            // Validate that the order has been paid
            if ($order->status !== 'pagado') {
                throw new Exception('The order must be paid for before a shipment is generated.');
            }

            // Check if this order already has a shipment
            if ($order->shipment) {
                throw new Exception('The order already has a registered shipment');
            }

            // Verify that the order has a delivery address
            if (!$order->address) {
                throw new Exception('El pedido no tiene una dirección asociada');
            }

            // Create the shipment record in the database
            $shipment = Shipment::create([
                'order_id' => $order->id,
                'carrier' => $data['carrier'],
                // Generate a random 12-character tracking number if not provided
                'tracking' => $data['tracking'] ?? strtoupper(Str::random(12)),
                'estimated_date' => $data['estimated_date'],
                'status' => 'en_transito'
            ]);

            // If shipment status is 'en_transito', update the order status to 'enviado'
            if ($shipment->status === 'en_transito') {
                $order->update([
                    'status' => 'enviado'
                ]);
            }

            // Return the created shipment with order and address loaded
            return $shipment->load('order.address');
        });
    }

    /**
     * Update the status of a shipment and handle order completion.
     *
     * If the shipment status is changed to 'entregado', the associated
     * order status will also be updated to 'completado'.
     *
     * @param \App\Models\Shipment $shipment The Shipment model instance to update
     * @param string $status The new status (e.g., en_transito, entregado, cancelado)
     * @return \App\Models\Shipment The updated Shipment model
     */
    public function updateStatus(Shipment $shipment, string $status)
    {
        // Update the shipment status
        $shipment->status = $status;
        $shipment->save();

        // If shipment is delivered, mark the order as completed
        if ($status === 'entregado') {
            $shipment->order->status = 'completado';
            $shipment->order->save();
        }

        return $shipment;
    }
}
