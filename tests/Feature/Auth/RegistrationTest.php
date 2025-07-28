<?php

// tests/Feature/Auth/RegistrationTest.php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Domain\Models\Tenant;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    #[Test]
    public function new_users_can_register_as_customers(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        // THE FIX: Assert that the user is redirected to the marketplace index
        $response->assertRedirect(route('public.items.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'is_admin' => false,
        ]);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertDatabaseHas('customers', [
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    public function customers_cannot_access_the_filament_admin_panel(): void
    {
        Tenant::factory()->create();
        $customer = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($customer)->get('/admin/login');

        $response->assertRedirect();
    }
}
