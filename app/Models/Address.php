<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    /** @var array */
    protected $fillable = [
        'user_id',
        'street',
        'city',
        'state',
        'country',
        'postal_code'
    ];

    /**
     * Get the user that owns this address.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all orders for this address.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
