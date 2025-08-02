<x-mail::message>
# {{ __('mail.welcome_title', ['appName' => config('app.name'), 'userName' => $user->name]) }}

{{ __('mail.welcome_intro') }}

{{ __('mail.welcome_next_step') }}

{{ __('mail.welcome_ignore') }}

<x-mail::button :url="route('filament.admin.auth.login')">
{{ __('mail.welcome_cta') }}
</x-mail::button>

{{ __('mail.regards') }},<br>
{{ __('mail.seller_notification_team', ['appName' => config('app.name')]) }}
</x-mail::message>