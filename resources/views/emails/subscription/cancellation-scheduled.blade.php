<x-mail::message>
# {{ __('mail.subscription_cancellation_title') }}

{{-- Add a blank line above this for correct Markdown parsing --}}
{{ __('mail.subscription_cancellation_body', [
    'userName' => $user->name,
    'tenantName' => $tenant->name,
    'endDate' => $endDate->format('d/m/Y')
]) }}

<x-mail::button :url="route('my-account.subscription.manage')">
{{ __('mail.subscription_cancellation_cta') }}
</x--mail::button>

{{ __('mail.regards') }},<br>
{{ config('app.name') }}
</x-mail::message>