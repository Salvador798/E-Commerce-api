<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    /** @var array */
    protected $fillable = [
        'order_id',
        'carrier',
        'tracking',
        'estimated_date',
        'status',
    ];

    /**
     * Get the order of this shipment.
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
        return $this->hasOneThrough(
            Address::class,
            Order::class,
            'id',
            'id',
            'order_id',
            'address_id'
        );
    }
}
