<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payment\StorePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = $this->service->all();

        return response()->json([
            'status' => true,
            'message' => 'Payments retrieved successfully',
            'data' => PaymentResource::collection($payments)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentRequest $request)
    {
        try {
            $payment = $this->service->create($request->validated());

            return response()->json([
                'status' => true,
                'message' => 'Payment created successfully',
                'data' => new PaymentResource($payment)
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
