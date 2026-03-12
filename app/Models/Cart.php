<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    /** @var array */
    protected $fillable = [
        'user_id'
    ];

    /**
     * Get the user that owns the cart.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all items in the cart.
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
