<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    /** @var array */
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity'
    ];

    /**
     * Get the cart that owns this item.
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Get the product of this item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
