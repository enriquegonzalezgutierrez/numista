<?php

// src/Collection/UI/Public/Controllers/ContactSellerController.php

namespace Numista\Collection\UI\Public\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\UI\Public\Mail\ContactSellerMail;

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

        // Find the tenant owner's email.
        // We take the first user associated with the tenant.
        // In a more complex scenario, a tenant might have a specific contact email.
        $seller = $item->tenant->users()->first();

        if (! $seller || ! $seller->email) {
            // Log this critical error for the site administrator to review.
            Log::error("Could not find a seller email for tenant ID: {$item->tenant->id} regarding item ID: {$item->id}");

            // Return a generic error to the user without revealing internal details.
            return back()->with('error', __('public.contact_modal_error_seller'));
        }

        try {
            // Send the email and queue it for better performance.
            Mail::to($seller->email)->send(
                new ContactSellerMail($item, $data['name'], $data['email'], $data['message'])
            );
        } catch (\Exception $e) {
            // Catch potential mail sending failures (e.g., mail server is down).
            Log::error('Failed to send contact seller email: '.$e->getMessage());

            return back()->with('error', 'There was a problem sending your message. Please try again later.');
        }

        // Return with a success message.
        return back()->with('success', __('public.contact_modal_success'));
    }
}
