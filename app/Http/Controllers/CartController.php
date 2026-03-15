<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\StoreCartItemRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Http\Resources\CartResource;
use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct(private CartService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCartItemRequest $request)
    {
        $userId = Auth::id();

        $cart = $this->service->getCartUser($userId);

        $this->service->addProduct($cart, $request->validated());

        $cart->load('items.product');

        return response()->json([
            'status' => true,
            'message' => 'Product added to cart successfully',
            'data' => new CartResource($cart)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $userId = Auth::id();

        $cart = $this->service->getCartUser($userId);

        return response()->json([
            'status' => true,
            'message' => 'Cart retrieved successfully',
            'data' => new CartResource($cart)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCartItemRequest $request, CartItem $item)
    {
        $cart = $this->service->updateItem($item, $request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Cart updated successfully',
            'data' => new CartResource($cart)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CartItem $item)
    {
        $this->service->removeItem($item);
        return response()->json([
            'status' => true,
            'message' => 'Item removed from cart successfully'
        ], 204);
    }

    public function empty($userId)
    {
        $cart = $this->service->getCartUser($userId);
        $this->service->empty($cart);

        return response()->json([
            'status' => true,
            'message' => 'Cart emptied successfully'
        ], 204);
    }
}
