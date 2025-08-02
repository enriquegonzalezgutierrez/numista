<?php

// tests/TestCase.php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Numista\Collection\Domain\Models\Item;

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

        // This remains important for URL consistency.
        $app['config']->set('app.url', 'http://localhost');
        $app['config']->set('app.name', 'Numista');
        $app['config']->set('app.locale', 'es');

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();

        // THE FINAL FIX: Prepare the Scout environment before every test.
        // This ensures the `test_items` index exists and is correctly configured.
        $this->artisan('scout:flush', ['model' => Item::class]);
        $this->artisan('scout:setup');

        $this->withoutMiddleware(VerifyCsrfToken::class);
        $this->withoutVite();
    }

    /**
     * Scrubs dynamic content from HTML snapshots to ensure consistency.
     */
    protected function scrubSnapshot(string $content): string
    {
        // Replace dynamic numeric IDs in various URLs
        $content = preg_replace('/(\/images\/)\d+/', '$1[id]', $content);
        $content = preg_replace('/(\/cart\/add\/)\d+(\/async)?/', '$1[id]$2', $content);
        $content = preg_replace('/(\/items\/)([a-zA-Z0-9-]+)/', '$1[slug]', $content);
        $content = preg_replace('/(\/orders\/)\d+/', '$1[id]', $content);

        // Replace dynamic IDs in cart update/remove forms
        $content = preg_replace('/(\/cart\/(update|remove)\/)\d+/', '$1[id]', $content);

        // Replace dynamic IDs in HTML attributes like `for` and `id`
        $content = preg_replace('/(for|id)="quantity-\d+"/', '$1="quantity-[id]"', $content);

        // THE FIX: Scrub CSRF tokens from meta tags, input fields, and Alpine/Livewire attributes.
        $content = preg_replace('/<meta name="csrf-token" content=".*">/', '<meta name="csrf-token" content="[FILTERED]">', $content);
        $content = preg_replace('/<input type="hidden" name="_token" value=".*" autocomplete="off">/', '<input type="hidden" name="_token" value="[FILTERED]" autocomplete="off">', $content);
        $content = preg_replace('/(\'X-CSRF-TOKEN\': \')([a-zA-Z0-9]+)(\',)/', '$1[FILTERED]$3', $content);
        $content = preg_replace('/(data-csrf=")([a-zA-Z0-9]+)(")/', '$1[FILTERED]$3', $content);

        return $content;
    }
}
