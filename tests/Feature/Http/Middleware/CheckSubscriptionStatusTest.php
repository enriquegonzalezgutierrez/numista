<?php

// tests/Feature/Http/Middleware/CheckSubscriptionStatusTest.php

namespace Tests\Feature\Http\Middleware;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Domain\Models\Tenant;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CheckSubscriptionStatusTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function an_unsubscribed_tenant_is_redirected_from_panel_to_subscription_page(): void
    {
        $tenant = Tenant::factory()->create(['subscription_status' => null]);

        /** @var \App\Models\User $user */
        $user = User::factory()->admin()->create();
        $user->tenants()->attach($tenant);

        $this->actingAs($user)
            ->get("/admin/{$tenant->slug}")
            ->assertRedirect(route('subscription.create', $tenant));
    }

    #[Test]
    public function a_subscribed_tenant_can_access_the_panel(): void
    {
        $tenant = Tenant::factory()->create(['subscription_status' => 'active']);

        /** @var \App\Models\User $user */
        $user = User::factory()->admin()->create();
        $user->tenants()->attach($tenant);

        $this->actingAs($user)
            ->get("/admin/{$tenant->slug}")
            ->assertOk();
    }
}
