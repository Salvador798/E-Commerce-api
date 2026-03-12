<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /** @var array */
    protected $fillable = [
        'name',
        'description',
        'price',
        'images',
        'status',
    ];

    /** @var array */
    protected $casts = [
        'images' => 'array',
        'status' => 'boolean',
    ];

    /**
     * Get the inventory of this product.
     */
    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    /**
     * Get all categories of this product.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, "product_category");
    }
}
