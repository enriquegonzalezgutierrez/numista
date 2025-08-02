<x-mail::message>
{{-- Email Title --}}
# {{ __('mail.subscription_payment_failed_title') }}

{{-- Email Body --}}
{{ __('mail.subscription_payment_failed_body', ['userName' => $user->name, 'tenantName' => $tenant->name]) }}

{{-- Call to Action Button --}}
<x-mail::button :url="route('my-account.subscription.manage')">
{{ __('mail.subscription_payment_failed_cta') }}
</x-mail::button>

{{-- Salutation --}}
{{ __('mail.regards') }},<br>
{{ config('app.name') }}
</x-mail::message>