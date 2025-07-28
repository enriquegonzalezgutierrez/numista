@if ($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md relative" role="alert">
        <strong class="font-bold">{{ __('Whoops! Something went wrong.') }}</strong>
        <ul class="mt-3 list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <div>
        <label for="label" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Etiqueta (Ej: Casa, Trabajo)</label>
        <input type="text" name="label" id="label" value="{{ old('label', $address->label ?? '') }}" required 
               class="mt-1 block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:focus:ring-teal-500">
    </div>
    <div>
        <label for="recipient_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre del destinatario</label>
        <input type="text" name="recipient_name" id="recipient_name" value="{{ old('recipient_name', $address->recipient_name ?? '') }}" required 
               class="mt-1 block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:focus:ring-teal-500">
    </div>
    <div class="sm:col-span-2">
        <label for="street_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dirección</label>
        <input type="text" name="street_address" id="street_address" value="{{ old('street_address', $address->street_address ?? '') }}" required 
               class="mt-1 block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:focus:ring-teal-500">
    </div>
    <div>
        <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ciudad</label>
        <input type="text" name="city" id="city" value="{{ old('city', $address->city ?? '') }}" required 
               class="mt-1 block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:focus:ring-teal-500">
    </div>
    <div>
        <label for="postal_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Código Postal</label>
        <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $address->postal_code ?? '') }}" required 
               class="mt-1 block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:focus:ring-teal-500">
    </div>
    <div>
        <label for="state" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Provincia / Estado</label>
        <input type="text" name="state" id="state" value="{{ old('state', $address->state ?? '') }}" 
               class="mt-1 block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:focus:ring-teal-500">
    </div>
    <div>
        <label for="country_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">País</label>
        {{-- THE FIX: Dynamically generate options from the database --}}
        <select name="country_code" id="country_code" 
                class="mt-1 block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:focus:ring-teal-500">
            @foreach($countries as $code => $name)
                <option value="{{ $code }}" @selected(old('country_code', $address->country_code ?? 'ES') == $code)>{{ $name }}</option>
            @endforeach
        </select>
    </div>
    <div class="sm:col-span-2">
        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Teléfono (Opcional)</label>
        <input type="tel" name="phone" id="phone" value="{{ old('phone', $address->phone ?? '') }}" 
               class="mt-1 block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:focus:ring-teal-500">
    </div>
</div>