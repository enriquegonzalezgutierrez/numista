<?php

namespace Numista\Collection\Application\MyAccount;

use App\Models\User;
use Numista\Collection\Domain\Models\Address;

class AddressService
{
    public function createForUser(User $user, array $data): Address
    {
        return $user->customer->addresses()->create($data);
    }

    public function updateForUser(Address $address, array $data): bool
    {
        return $address->update($data);
    }

    public function deleteForUser(Address $address): ?bool
    {
        return $address->delete();
    }
}
