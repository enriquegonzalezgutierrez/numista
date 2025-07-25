<?php

// tests/Feature/Http/Public/ContactSellerTest.php

namespace Tests\Feature\Http\Public;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Tenant;
use Numista\Collection\UI\Public\Mail\ContactSellerMail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ContactSellerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_sends_an_email_to_the_seller_and_redirects_with_success_message(): void
    {
        // 1. Arrange
        Mail::fake(); // IMPORTANT: This stops emails from actually being sent

        $tenant = Tenant::factory()->create();
        $seller = User::factory()->create();
        $tenant->users()->attach($seller);
        $item = Item::factory()->create(['tenant_id' => $tenant->id]);

        $formData = [
            'name' => 'Potential Buyer',
            'email' => 'buyer@example.com',
            'message' => 'I am very interested in this item.',
        ];

        // 2. Act
        $response = $this->post(route('public.items.contact', $item), $formData);

        // 3. Assert
        $response->assertRedirect();
        $response->assertSessionHas('success', __('public.contact_modal_success'));

        // Assert that an email was queued for the correct seller
        Mail::assertQueued(ContactSellerMail::class, function (ContactSellerMail $mail) use ($seller) {
            return $mail->hasTo($seller->email);
        });

        // Assert the content of the email
        Mail::assertQueued(ContactSellerMail::class, function (ContactSellerMail $mail) use ($formData, $item) {
            return $mail->item->id === $item->id &&
                   $mail->fromName === $formData['name'] &&
                   $mail->body === $formData['message'];
        });
    }

    #[Test]
    public function it_shows_validation_errors_for_invalid_data(): void
    {
        // Arrange
        Mail::fake();
        $item = Item::factory()->create();

        // Act: Post with an empty 'message'
        $response = $this->post(route('public.items.contact', $item), [
            'name' => 'Test',
            'email' => 'test@test.com',
            'message' => '', // Invalid data
        ]);

        // Assert
        $response->assertSessionHasErrors('message');
        Mail::assertNotQueued(ContactSellerMail::class);
    }
}
