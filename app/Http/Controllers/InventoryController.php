<?php

namespace App\Http\Controllers;

use App\Http\Requests\Inventory\StoreInventoryRequest;
use App\Http\Requests\Inventory\UpdateInventoryRequest;
use App\Http\Resources\InventoryResource;
use App\Models\Inventory;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct(private InventoryService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $inventories = $this->service->all();

        return response()->json([
            'status' => true,
            'message' => 'Inventories retrieved successfully',
            'data' => InventoryResource::collection($inventories)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInventoryRequest $request)
    {
        $inventory = $this->service->create($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Inventory created successfully',
            'data' => new InventoryResource($inventory->load('product'))
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInventoryRequest $request, Inventory $inventory)
    {
        $inventory = $this->service->update($inventory, $request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Inventory updated successfully',
            'data' => new InventoryResource($inventory->load('product'))
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
