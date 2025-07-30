<?php

// tests/Unit/Policies/AddressPolicyTest.php

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Policies\AddressPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Domain\Models\Address;
use Numista\Collection\Domain\Models\Customer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AddressPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_user_can_view_their_own_address(): void
    {
        $policy = new AddressPolicy;
        $user = User::factory()->has(Customer::factory())->create();
        $address = Address::factory()->create(['customer_id' => $user->customer->id]);

        $this->assertTrue($policy->view($user, $address));
    }

    #[Test]
    public function a_user_cannot_view_another_users_address(): void
    {
        $policy = new AddressPolicy;
        $user = User::factory()->has(Customer::factory())->create();
        $anotherUser = User::factory()->has(Customer::factory())->create();
        $addressOfAnotherUser = Address::factory()->create(['customer_id' => $anotherUser->customer->id]);

        $this->assertFalse($policy->view($user, $addressOfAnotherUser));
    }

    #[Test]
    public function a_user_can_update_their_own_address(): void
    {
        $policy = new AddressPolicy;
        $user = User::factory()->has(Customer::factory())->create();
        $address = Address::factory()->create(['customer_id' => $user->customer->id]);

        $this->assertTrue($policy->update($user, $address));
    }

    #[Test]
    public function a_user_cannot_update_another_users_address(): void
    {
        $policy = new AddressPolicy;
        $user = User::factory()->has(Customer::factory())->create();
        $anotherUser = User::factory()->has(Customer::factory())->create();
        $addressOfAnotherUser = Address::factory()->create(['customer_id' => $anotherUser->customer->id]);

        $this->assertFalse($policy->update($user, $addressOfAnotherUser));
    }

    #[Test]
    public function a_user_can_delete_their_own_address(): void
    {
        $policy = new AddressPolicy;
        $user = User::factory()->has(Customer::factory())->create();
        $address = Address::factory()->create(['customer_id' => $user->customer->id]);

        $this->assertTrue($policy->delete($user, $address));
    }

    #[Test]
    public function a_user_cannot_delete_another_users_address(): void
    {
        $policy = new AddressPolicy;
        $user = User::factory()->has(Customer::factory())->create();
        $anotherUser = User::factory()->has(Customer::factory())->create();
        $addressOfAnotherUser = Address::factory()->create(['customer_id' => $anotherUser->customer->id]);

        $this->assertFalse($policy->delete($user, $addressOfAnotherUser));
    }

    #[Test]
    public function it_handles_a_user_that_is_not_yet_a_customer(): void
    {
        // This test ensures we don't get a "property of non-object" error
        // if a user somehow exists without a customer record.
        $policy = new AddressPolicy;
        $userWithoutCustomer = User::factory()->create(); // No customer attached
        $someAddress = Address::factory()->create();

        // The policy should gracefully fail instead of throwing an error.
        $this->assertFalse($policy->view($userWithoutCustomer, $someAddress));
        $this->assertFalse($policy->update($userWithoutCustomer, $someAddress));
        $this->assertFalse($policy->delete($userWithoutCustomer, $someAddress));
    }
}
