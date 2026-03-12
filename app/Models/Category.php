<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /** @var array */
    protected $fillable = [
        'name',
        'status'
    ];

    /** @var array */
    protected $casts = [
        'status' => 'boolean'
    ];

    /**
     * Get all products in this category.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_category');
    }
}
