<?php

namespace App\Services;

use App\Models\Inventory;
use Exception;

class InventoryService
{
    /**
     * Retrieve all inventory records with their associated products.
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of Inventory models with loaded products
     */
    public function all()
    {
        return Inventory::with('product')->get();
    }

    /**
     * Create a new inventory record in the database.
     *
     * @param array $data Inventory data (product_id, available_quantity, reserved_quantity, etc.)
     * @return \App\Models\Inventory The newly created Inventory model
     */
    public function create(array $data)
    {
        return Inventory::create($data);
    }

    /**
     * Update an existing inventory record with new data.
     *
     * @param \App\Models\Inventory $inventory The Inventory model instance to update
     * @param array $data New inventory data to replace existing values
     * @return \App\Models\Inventory The updated Inventory model
     */
    public function update(Inventory $inventory, array $data)
    {
        $inventory->update($data);
        return $inventory;
    }

    /**
     * Increase the available quantity of inventory stock.
     *
     * @param \App\Models\Inventory $inventory The Inventory model instance to update
     * @param int $quantity The amount to add to available stock
     * @return \App\Models\Inventory The updated Inventory model
     */
    public function increase(Inventory $inventory, int $quantity)
    {
        $inventory->available_quantity += $quantity;
        $inventory->save();
        return $inventory;
    }

    /**
     * Decrease the available quantity of inventory stock.
     *
     * @param \App\Models\Inventory $inventory The Inventory model instance to update
     * @param int $quantity The amount to subtract from available stock
     * @return \App\Models\Inventory The updated Inventory model
     * @throws \Exception If there is insufficient stock available
     */
    public function decrease(Inventory $inventory, int $quantity)
    {
        if ($inventory->available_quantity < $quantity) {
            throw new Exception('Insufficient stock');
        }

        $inventory->available_quantity -= $quantity;
        $inventory->save();
        return $inventory;
    }
}
