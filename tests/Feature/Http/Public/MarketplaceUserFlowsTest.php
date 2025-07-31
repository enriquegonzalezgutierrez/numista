<?php

// tests/Feature/Http/Public/MarketplaceUserFlowsTest.php

namespace Tests\Feature\Http\Public;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Tenant;
use Numista\Collection\Infrastructure\Mail\Contact\ContactSellerMail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MarketplaceUserFlowsTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private User $seller;

    private Item $item;

    protected function setUp(): void
    {
        parent::setUp();

        // Arrange: Create a common setup for all tests in this file
        $this->tenant = Tenant::factory()->create(['name' => 'The Seller Collection']);
        $this->seller = User::factory()->create();
        $this->tenant->users()->attach($this->seller);

        $this->item = Item::factory()->create([
            'tenant_id' => $this->tenant->id,
            'status' => 'for_sale',
        ]);
    }

    #[Test]
    public function it_can_display_a_public_tenant_profile_page_with_their_items(): void
    {
        // Arrange: Create another item for the same tenant
        $item2 = Item::factory()->create(['tenant_id' => $this->tenant->id, 'status' => 'for_sale']);

        // Arrange: Create an item that should NOT be visible (not for sale)
        Item::factory()->create(['tenant_id' => $this->tenant->id, 'status' => 'in_collection']);

        // Arrange: Create an item from another tenant that should NOT be visible
        $otherTenantItem = Item::factory()->create(['status' => 'for_sale']);

        // Act: Visit the public profile page of the first tenant
        $response = $this->get(route('public.tenants.show', $this->tenant));

        // Assert: The page loads successfully and shows the correct information
        $response->assertOk();
        $response->assertSee($this->tenant->name);
        $response->assertSee($this->item->name);
        $response->assertSee($item2->name);
        $response->assertDontSee($otherTenantItem->name);
        $response->assertViewHas('items', function ($items) {
            return $items->count() === 2; // Verify only the 2 items for sale are passed to the view
        });
    }

    #[Test]
    public function the_item_details_page_links_to_the_tenant_profile_page(): void
    {
        // Act: Visit the item's detail page
        $response = $this->get(route('public.items.show', $this->item));

        // Assert: The response contains a link to the tenant's profile page
        $response->assertOk();
        $response->assertSee(route('public.tenants.show', $this->tenant));
    }

    #[Test]
    public function a_user_can_send_a_contact_message_to_the_seller(): void
    {
        // Arrange: Fake the mailer to prevent actual emails from being sent
        Mail::fake();

        $formData = [
            'name' => 'Potential Buyer',
            'email' => 'buyer@example.com',
            'message' => 'I am interested in this item!',
        ];

        // Act: Post the form data to the contact endpoint
        $response = $this->post(route('public.items.contact', $this->item), $formData);

        // Assert: The user is redirected back with a success message
        $response->assertRedirect();
        $response->assertSessionHas('success', __('public.contact_modal_success'));

        // Assert: A mailable was queued to be sent to the correct seller
        Mail::assertQueued(ContactSellerMail::class, function (ContactSellerMail $mail) {
            return $mail->hasTo($this->seller->email);
        });

        // Assert: The queued email contains the correct data
        Mail::assertQueued(ContactSellerMail::class, function (ContactSellerMail $mail) use ($formData) {
            return $mail->fromName === $formData['name']
                && $mail->fromEmail === $formData['email']
                && $mail->body === $formData['message']
                && $mail->item->id === $this->item->id;
        });
    }

    #[Test]
    public function contact_form_returns_validation_error_for_missing_message(): void
    {
        Mail::fake();

        $formData = [
            'name' => 'Potential Buyer',
            'email' => 'buyer@example.com',
            'message' => '', // Message is empty
        ];

        $response = $this->post(route('public.items.contact', $this->item), $formData);

        $response->assertSessionHasErrors('message');
        Mail::assertNothingQueued();
    }
}
