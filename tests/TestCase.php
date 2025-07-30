<?php

// tests/TestCase.php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
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

        // Esto es crucial para la consistencia
        $app['config']->set('app.url', 'http://localhost');
        $app['config']->set('app.name', 'Numista');
        $app['config']->set('app.locale', 'es');

        return $app;
    }

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // THE FIX: Disable CSRF protection middleware for all tests.
        // This prevents the CSRF token from changing on every request,
        // which would cause snapshot tests to fail constantly.
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $this->withoutVite();
    }
}
