<x-mail::message>
# {{ __('mail.subscription_activated_title') }}

{{ __('mail.hello') }} {{ $user->name }},

{{ __('mail.subscription_activated_body', ['tenantName' => $tenant->name]) }}

<x-mail::button :url="route('filament.admin.pages.dashboard', ['tenant' => $tenant])">
{{ __('mail.welcome_cta') }}
</x-mail::button>

{{ __('mail.regards') }},<br>
{{ __('mail.seller_notification_team', ['appName' => config('app.name')]) }}
</x-mail::message>