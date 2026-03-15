<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    /**
     * Get all products with categories
     */
    public function all()
    {
        return Product::with('categories')->get();
    }

    /**
     * Get product by ID
     */
    public function getById($id)
    {
        return Product::with('categories')->findOrFail($id);
    }

    /**
     * Create product and sync categories
     */
    public function create(array $data)
    {
        // Extract categories from data before creating product
        $categories = $data['categories'] ?? [];

        // Remove categories from data array (not a direct product attribute)
        unset($data['categories']);

        // status
        $data['status'] = $data['status'] ?? true;

        // Create the product record in the database
        $product = Product::create($data);

        // Sync the categories (many-to-many relationship)
        $product->categories()->sync($categories);

        return $product;
    }

    /**
     * Update product and optionally categories
     */
    public function update(Product $product, array $data)
    {
        // Extract categories from data (if provided)
        $categories = $data['categories'] ?? null;

        // Remove categories from data array before updating product
        unset($data['categories']);

        // Update the product with new data
        $product->update($data);

        // Only sync categories if they were explicitly provided
        if ($categories !== null) {
            $product->categories()->sync($categories);
        }

        return $product;
    }


    /**
     * Toggle product status
     */
    public function toggle(Product $product)
    {
        $product->update([
            'status' => !$product->status
        ]);

        return $product->refresh();
    }
}
