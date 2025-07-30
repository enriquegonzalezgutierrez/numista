<?php

// tests/Unit/Http/Requests/StoreCheckoutRequestTest.php

namespace Tests\Unit\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Numista\Collection\Domain\Models\Address;
use Numista\Collection\Domain\Models\Customer;
use Numista\Collection\UI\Public\Requests\StoreCheckoutRequest;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StoreCheckoutRequestTest extends TestCase
{
    use RefreshDatabase;

    private function validate(array $data, User $user): \Illuminate\Contracts\Validation\Validator
    {
        $request = new StoreCheckoutRequest;
        $request->setUserResolver(fn () => $user);

        return Validator::make($data, $request->rules());
    }

    #[Test]
    public function it_fails_when_using_existing_address_but_id_is_missing(): void
    {
        $user = User::factory()->has(Customer::factory())->create();
        $validator = $this->validate(['address_option' => 'existing'], $user);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('selected_address_id'));
    }

    #[Test]
    public function it_fails_when_using_new_address_but_required_fields_are_missing(): void
    {
        $user = User::factory()->has(Customer::factory())->create();
        $validator = $this->validate(['address_option' => 'new'], $user);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('shipping_address.recipient_name'));
    }

    #[Test]
    public function it_passes_with_a_valid_existing_address(): void
    {
        $user = User::factory()->has(Customer::factory())->create();
        $address = Address::factory()->create(['customer_id' => $user->customer->id]);

        $validator = $this->validate([
            'address_option' => 'existing',
            'selected_address_id' => $address->id,
        ], $user);

        $this->assertFalse($validator->fails());
    }
}
