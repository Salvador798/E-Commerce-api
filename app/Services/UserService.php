<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    /**
     * Retrieve all users from the database.
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of User models
     */
    public function all()
    {
        // Fetch all user records from the users table
        return User::all();
    }

    /**
     * Retrieve a single user by ID.
     *
     * @param int $id The ID of the user
     * @return \App\Models\User The User model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If user is not found
     */
    public function getById($id)
    {
        // Find user by ID or throw ModelNotFoundException
        return User::findOrFail($id);
    }

    /**
     * Create a new user in the database.
     *
     * @param array $data User data (name, email, password, etc.)
     * @return \App\Models\User The newly created User model
     */
    public function create(array $data)
    {
        // Create and return a new user record
        return User::create($data);
    }

    /**
     * Update an existing user with new data.
     *
     * @param \App\Models\User $user The User model instance to update
     * @param array $data New user data to replace existing values
     * @return \App\Models\User The updated User model
     */
    public function update(User $user, array $data)
    {
        // Update user with new data and return the updated model
        $user->update($data);
        return $user;
    }

    /**
     * Remove an existing user from the database.
     *
     * @param \App\Models\User $user The User model instance to delete
     * @return void
     */
    public function delete(User $user)
    {
        // Delete the user record from the database
        $user->delete();
    }
}
