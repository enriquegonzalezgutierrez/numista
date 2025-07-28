<?php

namespace Numista\Collection\UI\Public\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Numista\Collection\Domain\Models\Address;

class AddressController extends Controller
{
    /**
     * Display a listing of the user's addresses.
     */
    public function index(): View
    {
        $addresses = Auth::user()->customer->addresses()->latest()->get();

        return view('public.my-account.addresses.index', compact('addresses'));
    }

    /**
     * Show the form for creating a new address.
     */
    public function create(): View
    {
        return view('public.my-account.addresses.create');
    }

    /**
     * Store a newly created address in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate($this->validationRules());

        Auth::user()->customer->addresses()->create($validatedData);

        return redirect()->route('my-account.addresses.index')->with('success', 'Dirección añadida con éxito.');
    }

    /**
     * Show the form for editing the specified address.
     */
    public function edit(Address $address): View
    {
        $this->authorizeOwnership($address);

        return view('public.my-account.addresses.edit', compact('address'));
    }

    /**
     * Update the specified address in storage.
     */
    public function update(Request $request, Address $address): RedirectResponse
    {
        $this->authorizeOwnership($address);

        $validatedData = $request->validate($this->validationRules());
        $address->update($validatedData);

        return redirect()->route('my-account.addresses.index')->with('success', 'Dirección actualizada con éxito.');
    }

    /**
     * Remove the specified address from storage.
     */
    public function destroy(Address $address): RedirectResponse
    {
        $this->authorizeOwnership($address);

        $address->delete();

        return redirect()->route('my-account.addresses.index')->with('success', 'Dirección eliminada con éxito.');
    }

    /**
     * Reusable validation rules for addresses.
     */
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

    /**
     * Security check to ensure a user can only manage their own addresses.
     */
    private function authorizeOwnership(Address $address): void
    {
        if ($address->customer_id !== Auth::user()->customer->id) {
            abort(403, 'This action is unauthorized.');
        }
    }
}
