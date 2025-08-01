<?php

// tests/Feature/Http/Public/Auth/TenantRegistrationTest.php

namespace Tests\Feature\Http\Public\Auth;

use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Numista\Collection\Domain\Models\Tenant;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TenantRegistrationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_tenant_registration_screen_can_be_rendered(): void
    {
        $this->get(route('register.seller'))->assertOk();
    }

    #[Test]
    public function a_new_tenant_and_user_can_be_registered(): void
    {
        Event::fake();

        $response = $this->post(route('register.seller.store'), [
            'tenant_name' => 'My Test Collection',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'john@example.com', 'is_admin' => true]);
        $this->assertDatabaseHas('tenants', ['name' => 'My Test Collection']);

        $tenant = Tenant::where('name', 'My Test Collection')->first();
        $response->assertRedirect(route('subscription.create', $tenant));

        Event::assertDispatched(Registered::class);
    }
}
