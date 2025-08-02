@props(['url'])

<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            <img src="{{ asset('storage/logo.png') }}"
                 class="logo"
                 alt="{{ config('app.name') }} Logo"
                 width="120" {{-- THE FIX #1: Add the robust HTML width attribute --}}
                 style="display: block; border: 0; max-width: 100%; width: 120px; height: auto; margin: 0 auto 10px;" {{-- THE FIX #2: Add height:auto to the style --}}
            >

            {{ $slot }}
        </a>
    </td>
</tr>