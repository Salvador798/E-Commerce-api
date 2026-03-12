<?php

namespace App\Services;

use App\Models\Address;

class AddressService
{
    /**
     * Retrieve all addresses associated with a specific user.
     *
     * @param int $userId The ID of the user
     * @return \Illuminate\Database\Eloquent\Collection Collection of Address models
     */
    public function getByUser($userId)
    {
        return Address::where('user_id', $userId)->get();
    }

    /**
     * Create a new address record in the database.
     *
     * @param array $data Address data (street, city, state, zip_code, country, user_id, etc.)
     * @return \App\Models\Address The newly created Address model
     */
    public function create(array $data)
    {
        return Address::create($data);
    }

    /**
     * Update an existing address with new data.
     *
     * @param \App\Models\Address $address The Address model instance to update
     * @param array $data New address data to replace existing values
     * @return \App\Models\Address The updated Address model
     */
    public function update(Address $address, array $data)
    {
        $address->update($data);
        return $address;
    }

    /**
     * Delete an address from the database.
     *
     * @param \App\Models\Address $address The Address model instance to delete
     * @return void
     */
    public function delete(Address $address)
    {
        $address->delete();
    }
}
