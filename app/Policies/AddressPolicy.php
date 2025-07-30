<?php

// app/Policies/AddressPolicy.php

namespace App\Policies;

use App\Models\User;
use Numista\Collection\Domain\Models\Address;

class AddressPolicy
{
    // ... viewAny and create methods remain the same ...

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Address $address): bool
    {
        // THE FIX: Check if the customer relationship exists before accessing its id.
        return $user->customer && $user->customer->id === $address->customer_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Address $address): bool
    {
        // THE FIX: Check if the customer relationship exists.
        return $user->customer && $user->customer->id === $address->customer_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Address $address): bool
    {
        // THE FIX: Check if the customer relationship exists.
        return $user->customer && $user->customer->id === $address->customer_id;
    }
}
