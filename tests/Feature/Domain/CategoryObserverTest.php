<?php

// tests/Feature/Domain/CategoryObserverTest.php

namespace Tests\Feature\Domain;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Domain\Models\Category;
use Numista\Collection\Domain\Models\Tenant;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CategoryObserverTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_automatically_generates_a_slug_when_creating_a_category(): void
    {
        $tenant = Tenant::factory()->create();
        $category = Category::factory()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Monedas de Oro',
        ]);

        $this->assertEquals('monedas-de-oro', $category->slug);
    }

    #[Test]
    public function it_generates_a_unique_slug_for_duplicate_category_names(): void
    {
        $tenant = Tenant::factory()->create();
        Category::factory()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Arte Clásico',
        ]);
        $newCategory = Category::factory()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Arte Clásico',
        ]);

        $this->assertEquals('arte-clasico-1', $newCategory->slug);
    }
}
