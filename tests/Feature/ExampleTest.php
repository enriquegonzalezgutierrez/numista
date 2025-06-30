<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase; // <-- Y AÑADE ESTA LÍNEA

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // To make this test pass, we need at least one category, because the
        // controller tries to fetch them. Let's create one.
        \Numista\Collection\Domain\Models\Category::factory()->create();

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
