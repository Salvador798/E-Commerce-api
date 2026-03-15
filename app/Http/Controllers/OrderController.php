<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with('user', 'items.product', 'payment', 'shipment')->get();

        return response()->json([
            'status' => true,
            'message' => 'Orders retrieved successfully',
            'data' => OrderResource::collection($orders)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $order = $this->service->create($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Order created successfully',
            'data' => new OrderResource($order->load('items.product', 'payment', 'shipment'))
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return response()->json([
            'status' => true,
            'message' => 'Order retrieved successfully',
            'data' => new OrderResource($order->load('items.product', 'payment', 'shipment'))
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
