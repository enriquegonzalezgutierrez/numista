<?php

// tests/Unit/Managers/ItemTypeManagerTest.php

namespace Tests\Unit\Managers;

use Numista\Collection\UI\Filament\ItemTypeManager;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ItemTypeManagerTest extends TestCase
{
    /**
     * The ItemTypeManager instance for testing.
     *
     * @var \Numista\Collection\UI\Filament\ItemTypeManager
     */
    private $manager;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new ItemTypeManager;
    }

    #[Test]
    public function it_returns_the_correct_form_components_for_a_known_type(): void
    {
        // Act: Request components for a type that has a handler class ('coin')
        $components = $this->manager->getFormComponentsForType('coin');

        // Assert: Verify that we got back an array of components
        $this->assertIsArray($components);
        $this->assertNotEmpty($components);

        // Assert that the returned components are actual Filament Section components
        foreach ($components as $component) {
            $this->assertInstanceOf(\Filament\Forms\Components\Section::class, $component);
        }
    }

    #[Test]
    public function it_returns_an_empty_array_for_an_unhandled_type(): void
    {
        // Act: Request components for a type that is registered but has a 'null' handler ('medal')
        $components = $this->manager->getFormComponentsForType('medal');

        // Assert: Verify that an empty array is returned
        $this->assertIsArray($components);
        $this->assertEmpty($components);
    }

    #[Test]
    public function it_returns_an_empty_array_for_an_unknown_type(): void
    {
        // Act: Request components for a type that does not exist at all
        $components = $this->manager->getFormComponentsForType('a_type_that_does_not_exist');

        // Assert: Verify that an empty array is returned
        $this->assertIsArray($components);
        $this->assertEmpty($components);
    }

    #[Test]
    public function it_returns_a_translated_list_of_types_for_a_select_field(): void
    {
        // Act: Get the array of options for the select field
        $options = $this->manager->getTypesForSelect();

        // Assert: Verify the structure and content
        $this->assertIsArray($options);
        $this->assertArrayHasKey('coin', $options, "The key 'coin' should exist.");
        $this->assertArrayHasKey('book', $options, "The key 'book' should exist.");

        // As our locale is 'es', the value for 'coin' should be 'Moneda'
        // Note: This relies on the translation files being loaded.
        $this->assertEquals('Moneda', $options['coin']);
    }
}
