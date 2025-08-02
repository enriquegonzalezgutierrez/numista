<x-mail::message>
# {{ __('mail.contact_title', ['itemName' => $item->name]) }}

{{ __('mail.contact_intro') }}

**{{ __('mail.contact_from') }}:** {{ $fromName }}
**{{ __('mail.contact_email') }}:** {{ $fromEmail }}

---

**{{ __('mail.contact_message') }}:**

<x-mail::panel>
{{ $body }}
</x-mail::panel>

<x-mail::button :url="route('public.items.show', $item)">
{{ __('mail.contact_view_item') }}
</x-mail::button>

{{ __('mail.contact_thanks') }}<br>
{{ config('app.name') }}
</x-mail::message>