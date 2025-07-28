<?php

namespace Tests\Feature\Http\Public;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Domain\Models\Address;
use Numista\Collection\Domain\Models\Country;
use Numista\Collection\Domain\Models\Customer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AddressControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private User $anotherUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->has(Customer::factory())->create();
        $this->anotherUser = User::factory()->has(Customer::factory())->create();

        Country::factory()->create(['name' => 'Spain', 'iso_code' => 'ES']);
        Country::factory()->create(['name' => 'United States', 'iso_code' => 'US']);
    }

    #[Test]
    public function guests_are_redirected_from_address_pages_to_login(): void
    {
        $this->get(route('my-account.addresses.index'))->assertRedirect(route('login'));
        $this->get(route('my-account.addresses.create'))->assertRedirect(route('login'));
    }

    #[Test]
    public function an_authenticated_user_can_view_their_addresses(): void
    {
        $address = Address::factory()->create(['customer_id' => $this->user->customer->id]);
        $this->actingAs($this->user)
            ->get(route('my-account.addresses.index'))
            ->assertStatus(200)
            ->assertSee($address->label);
    }

    #[Test]
    public function an_authenticated_user_cannot_view_other_users_addresses(): void
    {
        $addressOfAnotherUser = Address::factory()->create(['customer_id' => $this->anotherUser->customer->id]);
        $this->actingAs($this->user)
            ->get(route('my-account.addresses.index'))
            ->assertStatus(200)
            ->assertDontSee($addressOfAnotherUser->street_address);
    }

    #[Test]
    public function a_user_can_see_the_create_address_form(): void
    {
        $this->actingAs($this->user)
            ->get(route('my-account.addresses.create'))
            ->assertStatus(200)
            ->assertViewIs('public.my-account.addresses.create');
    }

    #[Test]
    public function a_user_can_create_a_new_address(): void
    {
        $addressData = ['label' => 'Home', 'recipient_name' => 'John Doe', 'street_address' => '123 Main St', 'city' => 'Anytown', 'postal_code' => '12345', 'country_code' => 'US'];

        $this->actingAs($this->user)
            ->post(route('my-account.addresses.store'), $addressData)
            ->assertRedirect(route('my-account.addresses.index'))
            ->assertSessionHas('success', trans('public.address_add_success'));

        $this->assertDatabaseHas('addresses', array_merge($addressData, ['customer_id' => $this->user->customer->id]));
    }

    #[Test]
    public function a_user_can_see_the_edit_address_form(): void
    {
        $address = Address::factory()->create(['customer_id' => $this->user->customer->id]);

        $this->actingAs($this->user)
            ->get(route('my-account.addresses.edit', $address))
            ->assertStatus(200)
            ->assertViewIs('public.my-account.addresses.edit')
            ->assertViewHas('address', $address);
    }

    #[Test]
    public function a_user_can_update_their_own_address(): void
    {
        $address = Address::factory()->create(['customer_id' => $this->user->customer->id]);
        $updatedData = ['label' => 'Work Office', 'recipient_name' => 'John H. Doe', 'street_address' => '456 Business Rd', 'city' => 'Metropolis', 'postal_code' => '54321', 'country_code' => 'ES'];

        $this->actingAs($this->user)
            ->patch(route('my-account.addresses.update', $address), $updatedData)
            ->assertRedirect(route('my-account.addresses.index'))
            ->assertSessionHas('success', trans('public.address_update_success'));

        $this->assertDatabaseHas('addresses', array_merge($updatedData, ['id' => $address->id]));
    }

    #[Test]
    public function a_user_cannot_update_another_users_address(): void
    {
        $addressOfAnotherUser = Address::factory()->create(['customer_id' => $this->anotherUser->customer->id]);

        $this->actingAs($this->user)
            ->patch(route('my-account.addresses.update', $addressOfAnotherUser), ['label' => 'Hacked'])
            ->assertStatus(403);
    }

    #[Test]
    public function a_user_can_see_the_delete_confirmation_page_for_their_own_address(): void
    {
        $address = Address::factory()->create(['customer_id' => $this->user->customer->id]);

        $this->actingAs($this->user)
            ->get(route('my-account.addresses.confirmDestroy', $address))
            ->assertStatus(200)
            ->assertViewIs('public.my-account.addresses.confirm-delete')
            ->assertViewHas('address', $address)
            ->assertSee(__('public.confirm_address_delete'));
    }

    #[Test]
    public function a_user_can_delete_their_own_address(): void
    {
        $address = Address::factory()->create(['customer_id' => $this->user->customer->id]);

        $this->actingAs($this->user)
            ->delete(route('my-account.addresses.destroy', $address))
            ->assertRedirect(route('my-account.addresses.index'))
            ->assertSessionHas('success', trans('public.address_delete_success'));

        $this->assertDatabaseMissing('addresses', ['id' => $address->id]);
    }

    #[Test]
    public function a_user_cannot_delete_another_users_address(): void
    {
        $addressOfAnotherUser = Address::factory()->create(['customer_id' => $this->anotherUser->customer->id]);

        $this->actingAs($this->user)
            ->delete(route('my-account.addresses.destroy', $addressOfAnotherUser))
            ->assertStatus(403);
    }
}
