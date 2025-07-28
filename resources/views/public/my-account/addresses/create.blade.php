@extends('layouts.account')

@section('title', 'Añadir Nueva Dirección')

@section('account-content')
<div class="p-6 text-gray-900 dark:text-gray-100">
    <h2 class="text-2xl font-semibold mb-6">Añadir Nueva Dirección</h2>

    <form action="{{ route('my-account.addresses.store') }}" method="POST">
        @csrf
        @include('public.my-account.addresses.partials.form-fields')
        <div class="mt-6 flex justify-end space-x-4">
            <a href="{{ route('my-account.addresses.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Cancelar</a>
            <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700">Guardar Dirección</button>
        </div>
    </form>
</div>
@endsection