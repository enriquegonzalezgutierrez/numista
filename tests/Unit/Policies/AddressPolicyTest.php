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

    /**
     * Helper to create a user with an associated customer profile.
     */
    private function createCustomerUser(): User
    {
        $user = User::factory()->customer()->create();
        Customer::factory()->create(['user_id' => $user->id]);

        return $user;
    }

    #[Test]
    public function a_user_can_view_their_own_address(): void
    {
        $policy = new AddressPolicy;
        $user = $this->createCustomerUser();
        $address = Address::factory()->create(['customer_id' => $user->customer->id]);

        $this->assertTrue($policy->view($user, $address));
    }

    #[Test]
    public function a_user_cannot_view_another_users_address(): void
    {
        $policy = new AddressPolicy;
        $user = $this->createCustomerUser();
        $anotherUser = $this->createCustomerUser();
        $addressOfAnotherUser = Address::factory()->create(['customer_id' => $anotherUser->customer->id]);

        $this->assertFalse($policy->view($user, $addressOfAnotherUser));
    }

    #[Test]
    public function a_user_can_update_their_own_address(): void
    {
        $policy = new AddressPolicy;
        $user = $this->createCustomerUser();
        $address = Address::factory()->create(['customer_id' => $user->customer->id]);

        $this->assertTrue($policy->update($user, $address));
    }

    #[Test]
    public function a_user_cannot_update_another_users_address(): void
    {
        $policy = new AddressPolicy;
        $user = $this->createCustomerUser();
        $anotherUser = $this->createCustomerUser();
        $addressOfAnotherUser = Address::factory()->create(['customer_id' => $anotherUser->customer->id]);

        $this->assertFalse($policy->update($user, $addressOfAnotherUser));
    }

    #[Test]
    public function a_user_can_delete_their_own_address(): void
    {
        $policy = new AddressPolicy;
        $user = $this->createCustomerUser();
        $address = Address::factory()->create(['customer_id' => $user->customer->id]);

        $this->assertTrue($policy->delete($user, $address));
    }

    #[Test]
    public function a_user_cannot_delete_another_users_address(): void
    {
        $policy = new AddressPolicy;
        $user = $this->createCustomerUser();
        $anotherUser = $this->createCustomerUser();
        $addressOfAnotherUser = Address::factory()->create(['customer_id' => $anotherUser->customer->id]);

        $this->assertFalse($policy->delete($user, $addressOfAnotherUser));
    }

    #[Test]
    public function it_handles_a_user_that_is_an_admin_and_not_a_customer(): void
    {
        $policy = new AddressPolicy;
        $adminUser = User::factory()->admin()->create(); // This user will not have a customer profile
        $customerForAddress = Customer::factory()->create(); // Creates its own user
        $someAddress = Address::factory()->create(['customer_id' => $customerForAddress->id]);

        $this->assertFalse($policy->view($adminUser, $someAddress));
        $this->assertFalse($policy->update($adminUser, $someAddress));
        $this->assertFalse($policy->delete($adminUser, $someAddress));
    }
}
