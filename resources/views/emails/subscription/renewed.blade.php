<x-mail::message>
{{-- Email Title --}}
# {{ __('mail.subscription_renewed_title') }}

{{-- Email Body --}}
{{ __('mail.subscription_renewed_body', ['userName' => $user->name, 'tenantName' => $tenant->name]) }}

{{-- Call to Action Button --}}
<x-mail::button :url="route('filament.admin.pages.dashboard', ['tenant' => $tenant])">
{{ __('mail.seller_notification_cta') }}
</x-mail::button>

{{-- Salutation --}}
{{ __('mail.regards') }},<br>
{{ config('app.name') }}
</x-mail::message>