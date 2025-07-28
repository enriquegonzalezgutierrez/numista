@extends('layouts.account')

@section('title', 'Mis Direcciones')

@section('account-content')
<div class="p-6 text-gray-900 dark:text-gray-100">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Mis Direcciones</h2>
        <a href="{{ route('my-account.addresses.create') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700 active:bg-teal-900 focus:outline-none focus:border-teal-900 focus:ring ring-teal-300 disabled:opacity-25 transition ease-in-out duration-150">
            Añadir Dirección
        </a>
    </div>

    @if($addresses->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($addresses as $address)
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                    <h3 class="font-bold text-lg">{{ $address->label }}</h3>
                    <address class="mt-2 not-italic text-sm text-gray-600 dark:text-gray-300">
                        {{ $address->recipient_name }}<br>
                        {{ $address->street_address }}<br>
                        {{ $address->postal_code }} {{ $address->city }}, {{ $address->state }}<br>
                        {{-- In a real app, you would convert country code to country name --}}
                        {{ $address->country_code }}<br>
                        @if($address->phone)
                            Tlf: {{ $address->phone }}
                        @endif
                    </address>
                    <div class="mt-4 flex items-center space-x-4">
                        <a href="{{ route('my-account.addresses.edit', $address) }}" class="text-sm font-medium text-teal-600 hover:text-teal-500">Editar</a>
                        <form action="{{ route('my-account.addresses.destroy', $address) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta dirección?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-500">Eliminar</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8">
            <p class="text-gray-500 dark:text-gray-400">No tienes ninguna dirección guardada.</p>
        </div>
    @endif
</div>
@endsection