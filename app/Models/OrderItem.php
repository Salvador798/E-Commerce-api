<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    /** @var array */
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'emit_price'
    ];

    /**
     * Get the order that owns this item.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product of this item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
