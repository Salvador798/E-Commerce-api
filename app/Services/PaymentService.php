<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Exception;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Retrieve all payment records with their associated orders.
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of Payment models with loaded orders
     */
    public function all()
    {
        return Payment::with('order')->get();
    }

    /**
     * Create a new payment for an order with validation.
     *
     * This method performs the following operations within a database transaction:
     * 1. Validates that the order exists
     * 2. Checks if the order already has a payment
     * 3. Validates that the payment amount matches the order total
     * 4. Creates the payment record
     * 5. Updates the order status if payment is successful
     *
     * @param array $data Payment data containing:
     *                    - order_id: The ID of the order to pay
     *                    - amount: The payment amount
     *                    - method: The payment method (e.g., credit_card, cash, transfer)
     *                    - status: The payment status (e.g., pending, paid, failed)
     * @return \App\Models\Payment The created Payment model
     * @throws \Exception If the order already has a payment or if the amount doesn't match
     */
    public function create(array $data)
    {
        // Use a database transaction to ensure data consistency
        return DB::transaction(function () use ($data) {

            // Fetch the order from the database
            $order = Order::findOrFail($data['order_id']);

            // Check if this order already has a payment associated
            if ($order->payment) {
                throw new Exception('El pedido ya tiene un pago registrado');
            }

            // Verify that the payment amount matches the order total
            if ($order->total != $data['amount']) {
                throw new Exception('El monto no coincide con el total del pedido');
            }

            // Create the payment record in the database
            $payment = Payment::create([
                'order_id' => $order->id,
                'date' => now(),
                'amount' => $data['amount'],
                'method' => $data['method'],
                'status' => $data['status']
            ]);

            // If payment status is 'paid', update the order status to 'pagado'
            if ($data['status'] === 'paid') {
                $order->update([
                    'status' => 'pagado'
                ]);
            }

            // Return the created payment
            return $payment;
        });
    }
}
