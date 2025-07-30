<?php

namespace Numista\Collection\UI\Public\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Numista\Collection\Application\MyAccount\AddressService;
use Numista\Collection\Domain\Models\Address;
use Numista\Collection\Domain\Models\Country;
use Numista\Collection\UI\Public\Requests\AddressRequest;

class AddressController extends Controller
{
    public function __construct(private AddressService $addressService) {}

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

    public function store(AddressRequest $request): RedirectResponse
    {
        $this->addressService->createForUser(Auth::user(), $request->validated());

        return redirect()->route('my-account.addresses.index')->with('success', __('public.address_add_success'));
    }

    public function edit(Address $address): View
    {
        $this->authorize('update', $address);
        $countries = Country::orderBy('name')->pluck('name', 'iso_code');
        $user = Auth::user();

        return view('public.my-account.addresses.edit', compact('address', 'countries', 'user'));
    }

    public function update(AddressRequest $request, Address $address): RedirectResponse
    {
        $this->authorize('update', $address);
        $this->addressService->updateForUser($address, $request->validated());

        return redirect()->route('my-account.addresses.index')->with('success', __('public.address_update_success'));
    }

    public function confirmDestroy(Address $address): View
    {
        $this->authorize('delete', $address);

        return view('public.my-account.addresses.confirm-delete', compact('address'));
    }

    public function destroy(Address $address): RedirectResponse
    {
        $this->authorize('delete', $address);
        $this->addressService->deleteForUser($address);

        return redirect()->route('my-account.addresses.index')->with('success', __('public.address_delete_success'));
    }
}
