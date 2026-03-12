<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\StoreCartItemRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Http\Request;

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
    public function store(StoreCartItemRequest $request, $userId)
    {
        $cart = $this->service->getCartUser($userId);
        $item = $this->service->addProduct($cart, $request->validated());

        return response()->json($item);
    }

    /**
     * Display the specified resource.
     */
    public function show($userId)
    {
        return response()->json(
            $this->service->getCartUser($userId)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCartItemRequest $request, CartItem $item)
    {
        return response()->json(
            $this->service->updateItem($item, $request->validated())
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CartItem $item)
    {
        $this->service->removeItem($item);
        return response()->json(null, 204);
    }

    public function empty($userId)
    {
        $cart = $this->service->getCartUser($userId);
        $this->service->empty($cart);

        return response()->json(null, 204);
    }
}
