<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    /**
     * Retrieve all products with their associated categories.
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of Product models with loaded categories
     */
    public function all()
    {
        return Product::with('categories')->get();
    }

    /**
     * Retrieve a single product by ID with its associated categories.
     *
     * @param int $id The ID of the product
     * @return \App\Models\Product The Product model with loaded categories
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If product is not found
     */
    public function getById($id)
    {
        return Product::with('categories')->findOrFail($id);
    }

    /**
     * Create a new product with associated categories.
     *
     * @param array $data Product data (name, description, price, sku, status, etc.)
     *                    - categories: (optional) Array of category IDs to associate
     * @return \App\Models\Product The newly created Product model
     */
    public function create(array $data)
    {
        // Extract categories from data before creating product
        $categories = $data['categories'] ?? [];

        // Remove categories from data array (not a direct product attribute)
        unset($data['categories']);

        // Create the product record in the database
        $product = Product::create($data);

        // Sync the categories (many-to-many relationship)
        $product->categories()->sync($categories);

        return $product;
    }

    /**
     * Update an existing product with new data and optionally update categories.
     *
     * @param \App\Models\Product $product The Product model instance to update
     * @param array $data New product data to replace existing values
     *                    - categories: (optional) Array of category IDs to sync
     * @return \App\Models\Product The updated Product model
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
     * Toggle the status of a product between active and inactive.
     *
     * If the product is active (true), it becomes inactive (false),
     * and if it is inactive, it becomes active.
     *
     * @param \App\Models\Product $product The Product model instance to toggle
     * @return \App\Models\Product The updated Product model with refreshed data
     */
    public function toggle(Product $product)
    {
        $product->update([
            'status' => !$product->status
        ]);

        return $product->refresh();
    }
}
