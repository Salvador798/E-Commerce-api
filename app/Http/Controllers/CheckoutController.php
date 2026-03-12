<?php

namespace App\Http\Controllers;

use App\Http\Requests\Checkout\CheckoutRequest;
use App\Services\CheckoutService;
use Exception;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(private CheckoutService $service) {}

    public function checkout(CheckoutRequest $request)
    {
        try {
            $result = $this->service->processCheckout($request->validated(), $request->user()->id);
            return response()->json($result, 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
