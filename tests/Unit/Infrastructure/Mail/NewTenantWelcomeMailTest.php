<?php

// tests/Unit/Infrastructure/Mail/NewTenantWelcomeMailTest.php

namespace Tests\Unit\Infrastructure\Mail;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Numista\Collection\Infrastructure\Mail\Auth\NewTenantWelcomeMail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NewTenantWelcomeMailTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_welcome_email_contains_the_correct_content_and_links(): void
    {
        /** @var User $user */
        $user = User::factory()->make(['name' => 'John Doe']); // Use make() as we don't need to save it

        $mailable = new NewTenantWelcomeMail($user);

        // Render the Mailable and assert its content
        $mailable->assertSeeInHtml('¡Bienvenido a Numista, John Doe!');
        $mailable->assertSeeInHtml('Gracias por registrar tu colección con nosotros.');
        $mailable->assertSeeInHtml(route('filament.admin.auth.login'));
    }
}
