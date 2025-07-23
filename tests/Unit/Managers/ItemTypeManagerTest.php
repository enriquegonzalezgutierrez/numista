<?php

// tests/Unit/Managers/ItemTypeManagerTest.php

namespace Tests\Unit\Managers;

use Numista\Collection\UI\Filament\ItemTypeManager;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ItemTypeManagerTest extends TestCase
{
    private ItemTypeManager $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new ItemTypeManager;
    }

    #[Test]
    public function it_returns_a_translated_and_sorted_list_of_types_for_a_select_field(): void
    {
        $options = $this->manager->getTypesForSelect();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('coin', $options);
        $this->assertEquals('Moneda', $options['coin']);

        // Check if it's sorted alphabetically by translated value
        $sortedOptions = $options;
        asort($sortedOptions);
        $this->assertEquals($sortedOptions, $options, 'The types are not sorted correctly by their translated values.');
    }
}
