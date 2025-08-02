<?php

// tests/Feature/Http/Public/AddressPolicyTest.php

namespace Tests\Feature\Http\Public;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Domain\Models\Address;
use Numista\Collection\Domain\Models\Customer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AddressPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_user_cannot_view_or_edit_another_users_address_page(): void
    {
        /** @var User $userOne */
        $userOne = User::factory()->has(Customer::factory())->create();

        /** @var User $userTwo */
        $userTwo = User::factory()->has(Customer::factory())->create();
        $addressOfUserTwo = Address::factory()->create(['customer_id' => $userTwo->customer->id]);

        // Attempt to access the edit page
        $this->actingAs($userOne)
            ->get(route('my-account.addresses.edit', $addressOfUserTwo))
            ->assertForbidden(); // Expect a 403 Forbidden response

        // Attempt to access the delete confirmation page
        $this->actingAs($userOne)
            ->get(route('my-account.addresses.confirmDestroy', $addressOfUserTwo))
            ->assertForbidden();
    }

    #[Test]
    public function a_user_cannot_update_or_delete_another_users_address_data(): void
    {
        /** @var User $userOne */
        $userOne = User::factory()->has(Customer::factory())->create();

        /** @var User $userTwo */
        $userTwo = User::factory()->has(Customer::factory())->create();
        $addressOfUserTwo = Address::factory()->create(['customer_id' => $userTwo->customer->id, 'label' => 'Original Label']);

        // Attempt to send an update request
        $this->actingAs($userOne)
            ->patch(route('my-account.addresses.update', $addressOfUserTwo), ['label' => 'Hacked'])
            ->assertForbidden();

        // Attempt to send a delete request
        $this->actingAs($userOne)
            ->delete(route('my-account.addresses.destroy', $addressOfUserTwo))
            ->assertForbidden();

        // Assert that the address was not changed or deleted
        $this->assertDatabaseHas('addresses', ['id' => $addressOfUserTwo->id, 'label' => 'Original Label']);
    }
}
