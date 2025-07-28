<?php

namespace Numista\Collection\UI\Public\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Numista\Collection\Domain\Models\Address;
use Numista\Collection\Domain\Models\Country;

class AddressController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $addresses = $user->customer->addresses()->latest()->get();

        return view('public.my-account.addresses.index', compact('addresses', 'user'));
    }

    public function create(): View
    {
        $countries = Country::orderBy('name')->pluck('name', 'iso_code');
        $user = Auth::user();

        return view('public.my-account.addresses.create', compact('countries', 'user'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate($this->validationRules());
        Auth::user()->customer->addresses()->create($validatedData);

        return redirect()->route('my-account.addresses.index')->with('success', __('public.address_add_success'));
    }

    public function edit(Address $address): View
    {
        $this->authorizeOwnership($address);
        $countries = Country::orderBy('name')->pluck('name', 'iso_code');
        $user = Auth::user();

        return view('public.my-account.addresses.edit', compact('address', 'countries', 'user'));
    }

    public function update(Request $request, Address $address): RedirectResponse
    {
        $this->authorizeOwnership($address);
        $validatedData = $request->validate($this->validationRules());
        $address->update($validatedData);

        return redirect()->route('my-account.addresses.index')->with('success', __('public.address_update_success'));
    }

    public function destroy(Address $address): RedirectResponse
    {
        $this->authorizeOwnership($address);
        $address->delete();

        return redirect()->route('my-account.addresses.index')->with('success', __('public.address_delete_success'));
    }

    private function validationRules(): array
    {
        return [
            'label' => 'required|string|max:255',
            'recipient_name' => 'required|string|max:255',
            'street_address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country_code' => 'required|string|size:2',
            'phone' => 'nullable|string|max:20',
        ];
    }

    private function authorizeOwnership(Address $address): void
    {
        if ($address->customer_id !== Auth::user()->customer->id) {
            abort(403, 'This action is unauthorized.');
        }
    }
}
