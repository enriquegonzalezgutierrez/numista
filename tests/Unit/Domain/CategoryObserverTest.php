<?php

// tests/Feature/Domain/CategoryObserverTest.php

namespace Tests\Unit\Domain;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Domain\Models\Category;
// THE FIX: Tenant model is no longer needed for this test.
// use Numista\Collection\Domain\Models\Tenant;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CategoryObserverTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_automatically_generates_a_slug_when_creating_a_category(): void
    {
        // THE FIX: Removed tenant creation and association.
        $category = Category::factory()->create([
            'name' => 'Monedas de Oro',
        ]);

        $this->assertEquals('monedas-de-oro', $category->slug);
    }

    #[Test]
    public function it_generates_a_unique_slug_for_duplicate_category_names(): void
    {
        // THE FIX: Removed tenant creation and association.
        Category::factory()->create([
            'name' => 'Arte Clásico',
        ]);
        $newCategory = Category::factory()->create([
            'name' => 'Arte Clásico',
        ]);

        $this->assertEquals('arte-clasico-1', $newCategory->slug);
    }
}
