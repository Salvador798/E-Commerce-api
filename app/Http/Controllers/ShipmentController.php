<?php

namespace App\Http\Controllers;

use App\Http\Requests\Shipment\StoreShipmentRequest;
use App\Http\Resources\ShipmentResource;
use App\Models\Shipment;
use App\Services\ShipmentService;
use Exception;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    public function __construct(private ShipmentService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shipments = $this->service->all();

        return response()->json([
            'status' => true,
            'message' => 'Shipments retrieved successfully',
            'data' => ShipmentResource::collection($shipments)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreShipmentRequest $request)
    {
        try {
            $shipment = $this->service->create($request->validated());

            return response()->json([
                'status' => true,
                'message' => 'Shipment created successfully',
                'data' => new ShipmentResource($shipment)
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
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
    public function update(Request $request, Shipment $shipment)
    {
        $request->validate([
            'status' => 'required|string|in:en_transito,entregado'
        ]);

        $shipment = $this->service->updateStatus($shipment, $request->status);

        return response()->json([
            'status' => true,
            'message' => 'Shipment status updated successfully',
            'data' => new ShipmentResource($shipment)
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
