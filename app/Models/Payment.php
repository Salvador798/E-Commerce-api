<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /** @var array */
    protected $fillable = [
        'order_id',
        'date',
        'amount',
        'method',
        'status',
    ];

    /**
     * Get the order that owns this payment.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the address through the order.
     */
    public function address()
    {
        return $this->order->address();
    }
}
