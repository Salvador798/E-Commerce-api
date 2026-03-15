<?php

namespace App\Http\Controllers;

use App\Http\Requests\Checkout\CheckoutRequest;
use App\Http\Resources\CheckoutResource;
use App\Services\CheckoutService;
use Exception;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(private CheckoutService $service) {}

    public function checkout(CheckoutRequest $request)
    {
        try {
            $result = $this->service->processCheckout(
                $request->validated(),
                $request->user()->id
            );

            return response()->json([
                'status' => true,
                'message' => 'Checkout completed successfully',
                'data' => new CheckoutResource($result)
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
