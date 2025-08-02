<?php

namespace Tests\Unit\Application\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Numista\Collection\Application\Listeners\SendTenantWelcomeNotification;
use Numista\Collection\Infrastructure\Mail\Auth\NewTenantWelcomeMail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SendTenantWelcomeNotificationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_sends_a_welcome_email_to_a_newly_registered_admin_user(): void
    {
        Mail::fake();

        // Arrange: Create an event with an admin user.
        /** @var User $adminUser */
        $adminUser = User::factory()->admin()->make(); // Using make() is fine as the listener only needs the user object.
        $event = new Registered($adminUser);

        // Act: Manually handle the event with the listener.
        $listener = new SendTenantWelcomeNotification;
        $listener->handle($event);

        // Assert: A mailable was queued for the admin user.
        Mail::assertQueued(NewTenantWelcomeMail::class, function ($mail) use ($adminUser) {
            return $mail->hasTo($adminUser->email);
        });
    }

    #[Test]
    public function it_does_not_send_a_welcome_email_to_a_regular_customer(): void
    {
        Mail::fake();

        // Arrange: Create an event with a non-admin (customer) user.
        /** @var User $customerUser */
        $customerUser = User::factory()->customer()->make();
        $event = new Registered($customerUser);

        // Act: Manually handle the event.
        $listener = new SendTenantWelcomeNotification;
        $listener->handle($event);

        // Assert: The welcome email was NOT queued for the customer.
        Mail::assertNotQueued(NewTenantWelcomeMail::class);
    }
}
