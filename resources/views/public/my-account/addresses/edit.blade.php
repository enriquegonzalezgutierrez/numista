@extends('layouts.account')

@section('title', 'Editar Dirección')

@section('account-content')
<div class="p-6 text-gray-900 dark:text-gray-100">
    <h2 class="text-2xl font-semibold mb-6">Editar Dirección</h2>

    <form action="{{ route('my-account.addresses.update', $address) }}" method="POST">
        @csrf
        @method('PATCH')
        {{-- THE FIX: Pass the countries collection to the partial --}}
        @include('public.my-account.addresses.partials.form-fields', ['countries' => $countries])
        <div class="mt-6 flex justify-end space-x-4">
            <a href="{{ route('my-account.addresses.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Cancelar</a>
            <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700">Actualizar Dirección</button>
        </div>
    </form>
</div>
@endsection