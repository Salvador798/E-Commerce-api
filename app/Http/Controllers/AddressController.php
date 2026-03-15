<?php

namespace App\Http\Controllers;

use App\Http\Requests\Address\StoreAddressRequest;
use App\Http\Requests\Address\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Services\AddressService;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function __construct(private AddressService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $addresses = $this->service->getByUser($request->user()->id);

        return response()->json([
            'status' => true,
            'message' => 'Addresses retrieved successfully',
            'data' => AddressResource::collection($addresses)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAddressRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $address = $this->service->create($data);
        return response()->json([
            'status' => true,
            'message' => 'Address created successfully',
            'data' => new AddressResource($address)
        ], 201);
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
    public function update(UpdateAddressRequest $request, Address $address)
    {
        $address = $this->service->update($address, $request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Address updated successfully',
            'data' => new AddressResource($address)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Address $address)
    {
        $this->service->delete($address);
        return response()->json([
            'status' => true,
            'message' => 'Address deleted successfully'
        ], 204);
    }
}
