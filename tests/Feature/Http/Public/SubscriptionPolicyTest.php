<?php

// tests/Feature/Http/Public/SubscriptionPolicyTest.php

namespace Tests\Feature\Http\Public;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Domain\Models\Tenant;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SubscriptionPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_user_cannot_access_the_subscription_page_of_another_users_tenant(): void
    {
        // Arrange: Create User A with their own tenant.
        /** @var User $userA */
        $userA = User::factory()->admin()->create();
        $tenantA = Tenant::factory()->create();
        $userA->tenants()->attach($tenantA);

        // Arrange: Create User B with their own tenant.
        /** @var User $userB */
        $userB = User::factory()->admin()->create();
        $tenantB = Tenant::factory()->create();
        $userB->tenants()->attach($tenantB);

        // Act & Assert: User A tries to access the subscription page of Tenant B.
        // The controller's authorization logic should prevent this.
        $this->actingAs($userA)
            ->get(route('subscription.create', $tenantB))
            ->assertForbidden(); // We expect a 403 Forbidden response.
    }

    #[Test]
    public function a_user_can_access_the_subscription_page_of_their_own_tenant(): void
    {
        // Arrange: Create a user with their own tenant.
        /** @var User $user */
        $user = User::factory()->admin()->create();
        $tenant = Tenant::factory()->create();
        $user->tenants()->attach($tenant);

        // Act & Assert: The user accesses their own subscription page.
        $this->actingAs($user)
            ->get(route('subscription.create', $tenant))
            ->assertOk();
    }
}
