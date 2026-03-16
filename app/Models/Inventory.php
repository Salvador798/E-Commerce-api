<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    /** @var array */
    protected $fillable = [
        'product_id',
        'available_quantity',
    ];

    /** @var array */
    protected $casts = [
        'available_quantity' => 'integer'
    ];

    /**
     * Get the product of this inventory.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
