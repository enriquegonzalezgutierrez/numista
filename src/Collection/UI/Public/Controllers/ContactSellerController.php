<?php

namespace Numista\Collection\UI\Public\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Infrastructure\Mail\Contact\ContactSellerMail;

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

        $seller = $item->tenant->users()->first();

        if (! $seller || ! $seller->email) {
            Log::error("Could not find a seller email for tenant ID: {$item->tenant->id} regarding item ID: {$item->id}");

            return back()->with('error', __('public.contact_modal_error_seller'));
        }

        try {
            Mail::to($seller->email)->send(
                new ContactSellerMail($item, $data['name'], $data['email'], $data['message'])
            );
        } catch (\Exception $e) {
            Log::error('Failed to send contact seller email: '.$e->getMessage());

            return back()->with('error', 'There was a problem sending your message. Please try again later.');
        }

        return back()->with('success', __('public.contact_modal_success'));
    }
}
