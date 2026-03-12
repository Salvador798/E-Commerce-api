<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /** @var array */
    protected $fillable = [
        'user_id',
        'address_id',
        'date',
        'status',
        'total'
    ];

    /**
     * Get the user that placed this order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the delivery address of this order.
     */
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Get all items in this order.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the payment for this order.
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Get the shipment for this order.
     */
    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }
}
