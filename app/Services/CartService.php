<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;

class CartService
{
    /**
     * Retrieve or create a cart for a specific user.
     *
     * @param int $userId The ID of the user
     * @return \App\Models\Cart The Cart model with loaded items and products
     */
    public function getCartUser($userId)
    {
        return Cart::with('items.product')->firstOrCreate([
            'user_id' => $userId
        ]);
    }

    /**
     * Add a product to the cart or update quantity if already exists.
     *
     * @param \App\Models\Cart $cart The Cart model instance
     * @param array $data Product data (product_id, quantity, etc.)
     * @return \App\Models\CartItem The created or updated CartItem model
     */
    public function addProduct(Cart $cart, array $data)
    {
        $item = $cart->items()->where('product_id', $data['product_id'])->first();

        if ($item) {
            $item->quantity += $data['quantity'];
            $item->save();
            return $item;
        }

        return $cart->items()->create($data);
    }

    /**
     * Update an existing cart item with new data.
     *
     * @param \App\Models\CartItem $item The CartItem model instance to update
     * @param array $data New data to replace existing values (e.g., quantity)
     * @return \App\Models\CartItem The updated CartItem model
     */
    public function updateItem(CartItem $item, array $data)
    {
        $item->update($data);
        return $item;
    }

    /**
     * Remove a cart item from the cart.
     *
     * @param \App\Models\CartItem $item The CartItem model instance to delete
     * @return void
     */
    public function removeItem(CartItem $item)
    {
        $item->delete();
    }

    /**
     * Remove all items from the cart.
     *
     * @param \App\Models\Cart $cart The Cart model instance to empty
     * @return void
     */
    public function empty(Cart $cart)
    {
        $cart->items()->delete();
    }
}
