<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    // Add this method to the class
    protected function setUp(): void
    {
        parent::setUp();

        // This disables Vite's manifest lookup during tests
        $this->withoutVite();
    }
}
