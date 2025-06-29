<?php

namespace Tests\Feature\Http;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Numista\Collection\Domain\Models\Tenant;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TenantFileControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a fake disk for testing to avoid touching the real filesystem
        Storage::fake('tenants');
    }

    #[Test]
    public function an_authenticated_user_can_access_a_file_from_their_tenant(): void
    {
        // Arrange
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create();
        $user->tenants()->attach($tenant);

        // Create a fake file in the fake storage
        $path = "tenant-{$tenant->id}/test.jpg";
        Storage::disk('tenants')->put($path, 'dummy content');

        // Act & Assert
        $this->actingAs($user)
            ->get('/tenant-files/'.$path)
            ->assertStatus(200);
    }

    #[Test]
    public function an_authenticated_user_cannot_access_a_file_from_another_tenant(): void
    {
        // Arrange
        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create(); // A different tenant
        $user = User::factory()->create();
        $user->tenants()->attach($tenant1); // User belongs to tenant 1

        // A file belonging to tenant 2
        $path = "tenant-{$tenant2->id}/secret.jpg";
        Storage::disk('tenants')->put($path, 'dummy content');

        // Act & Assert
        $this->actingAs($user)
            ->get('/tenant-files/'.$path)
            ->assertStatus(403); // We expect Forbidden
    }

    #[Test]
    public function a_guest_user_cannot_access_any_tenant_file(): void
    {
        // Arrange
        $tenant = Tenant::factory()->create();
        $path = "tenant-{$tenant->id}/test.jpg";
        Storage::disk('tenants')->put($path, 'dummy content');

        // Act & Assert
        $this->get('/tenant-files/'.$path)
            ->assertStatus(403); // We expect Forbidden
    }
}
