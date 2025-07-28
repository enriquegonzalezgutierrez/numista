<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * This is the central point for configuring the application for all tests.
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // THE DEFINITIVE FIX:
        // Force the application's locale to 'es' for the entire test suite.
        // This is done during the application's creation, ensuring all
        // subsequent service providers and components use the correct locale.
        $app['config']->set('app.locale', 'es');

        return $app;
    }

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // This disables Vite's manifest lookup during tests
        $this->withoutVite();
    }
}
