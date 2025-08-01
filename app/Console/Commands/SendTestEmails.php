<?php

// app/Console/Commands/SendTestEmails.php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Order;
use Numista\Collection\Domain\Models\Tenant;
use Numista\Collection\Infrastructure\Mail\Auth\NewTenantWelcomeMail;
use Numista\Collection\Infrastructure\Mail\Contact\ContactSellerMail;
use Numista\Collection\Infrastructure\Mail\Orders\NewOrderNotificationMail;
use Numista\Collection\Infrastructure\Mail\Orders\OrderConfirmationMail;
use Numista\Collection\Infrastructure\Mail\Subscription\SubscriptionCancellationScheduledMail;
use Numista\Collection\Infrastructure\Mail\Subscription\SubscriptionConfirmationMail;
use Numista\Collection\Infrastructure\Mail\Subscription\SubscriptionPaymentFailedMail;
use Numista\Collection\Infrastructure\Mail\Subscription\SubscriptionReactivatedMail;
use Numista\Collection\Infrastructure\Mail\Subscription\SubscriptionRenewedMail;

class SendTestEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-test {--mail= : The specific mailable class to send}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends one or all transactional emails to Mailpit for design testing.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $recipient = $this->ask('Enter the email address to send the test emails to', 'test@example.com');
        if (! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address provided.');

            return self::FAILURE;
        }
        $this->info("Sending test emails to: {$recipient}");

        // THE FIX: Find a tenant that is guaranteed to have a user associated.
        $tenant = Tenant::has('users')->first();
        if (! $tenant) {
            $this->error('Could not find a tenant with an associated user. Please seed the database (`make migrate-fresh`).');

            return self::FAILURE;
        }
        /** @var User $user */
        $user = $tenant->users()->first();

        // Prepare other necessary data
        $order = Order::with('customer', 'tenant.users', 'items.item')->first();
        $item = Item::with('tenant.users')->first();

        if (! $order || ! $item) {
            $this->error('Database is missing required Order or Item data. Please seed the database.');

            return self::FAILURE;
        }

        $cancellationEndDate = Carbon::now()->addMonth();

        // A map of all testable mailables.
        $mailables = [
            'welcome' => fn () => new NewTenantWelcomeMail($user),
            'order-confirmation' => fn () => new OrderConfirmationMail($order),
            'seller-notification' => fn () => new NewOrderNotificationMail($order),
            'contact-seller' => fn () => new ContactSellerMail($item, 'John Doe', 'john.doe@example.com', 'This is a test message about the item.'),
            'subscription-confirmation' => fn () => new SubscriptionConfirmationMail($tenant, $user),
            'renewal-success' => fn () => new SubscriptionRenewedMail($tenant, $user),
            'renewal-failed' => fn () => new SubscriptionPaymentFailedMail($tenant, $user),
            'cancellation-scheduled' => fn () => new SubscriptionCancellationScheduledMail($tenant, $user, $cancellationEndDate),
            'reactivated' => fn () => new SubscriptionReactivatedMail($tenant, $user),
        ];

        $specificMail = $this->option('mail');

        if ($specificMail) {
            if (! array_key_exists($specificMail, $mailables)) {
                $this->error("Mailable '{$specificMail}' not found. Available options: ".implode(', ', array_keys($mailables)));

                return self::FAILURE;
            }
            $this->info("Sending specific email: {$specificMail}...");
            Mail::to($recipient)->send($mailables[$specificMail]());
        } else {
            $this->info('Sending all available test emails...');
            foreach ($mailables as $name => $mailableClosure) {
                $this->line(" - Sending {$name}...");
                Mail::to($recipient)->send($mailableClosure());
            }
        }

        $this->info("\n✅ All done! Check Mailpit to see the results.");

        return self::SUCCESS;
    }
}
