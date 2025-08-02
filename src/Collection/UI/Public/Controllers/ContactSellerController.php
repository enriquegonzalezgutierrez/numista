<?php

// src/Collection/UI/Public/Controllers/ContactSellerController.php

namespace Numista\Collection\UI\Public\Controllers; // THE FIX: Update namespace to match DDD structure

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // For logging errors
use Illuminate\Support\Facades\Mail;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Infrastructure\Mail\Contact\ContactSellerMail; // We will create this Mailable next

class ContactSellerController extends Controller
{
    /**
     * Handle the incoming request to send a contact message to the item's seller.
     */
    public function __invoke(Request $request, Item $item): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // An item belongs to a tenant, and a tenant has users (sellers/admins).
        // We'll get the first user associated with the tenant.
        $seller = $item->tenant->users()->first();

        if (! $seller || ! $seller->email) {
            // Log an error for the site administrator to see.
            Log::error("Could not find a seller email for tenant ID: {$item->tenant->id} regarding item ID: {$item->id}");

            // Return a generic error to the user without revealing internal details.
            return back()->with('error', __('public.contact_modal_error_seller'));
        }

        try {
            // Send the email. Using `queue()` is crucial for performance.
            // It sends the email in the background without making the user wait.
            Mail::to($seller->email)->queue(
                new ContactSellerMail($item, $data['name'], $data['email'], $data['message'])
            );
        } catch (\Exception $e) {
            Log::error('Failed to queue contact seller email: '.$e->getMessage());

            return back()->with('error', 'There was a problem sending your message. Please try again later.');
        }

        return back()->with('success', __('public.contact_modal_success'));
    }
}
