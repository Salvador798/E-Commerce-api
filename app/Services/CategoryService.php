<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    /**
     * Retrieve all categories ordered alphabetically by name.
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of Category models
     */
    public function all()
    {
        return Category::orderBy('name')->get();
    }

    /**
     * Create a new category in the database.
     *
     * @param array $data Category data (name, description, status, etc.)
     * @return \App\Models\Category The newly created Category model
     */
    public function create(array $data)
    {
        $data['status'] = $data['status'] ?? true;
        return Category::create($data);
    }

    /**
     * Update an existing category with new data.
     *
     * @param \App\Models\Category $category The Category model instance to update
     * @param array $data New category data to replace existing values
     * @return \App\Models\Category The updated Category model
     */
    public function update(Category $category, array $data)
    {
        $category->update($data);
        return $category;
    }

    /**
     * Toggle the status of a category between active and inactive.
     *
     * If the category is active (true), it becomes inactive (false),
     * and if it is inactive, it becomes active.
     *
     * @param \App\Models\Category $category The Category model instance to toggle
     * @return \App\Models\Category The updated Category model with refreshed data
     */
    public function toggleStatus(Category $category)
    {
        $category->update([
            'status' => !$category->status
        ]);

        return $category->refresh();
    }
}
