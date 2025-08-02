<x-mail::message>
# {{ __('mail.subscription_reactivated_title') }}

{{-- Add a blank line above this for correct Markdown parsing --}}
{{ __('mail.subscription_reactivated_body', ['userName' => $user->name, 'tenantName' => $tenant->name]) }}

{{ __('mail.regards') }},<br>
{{ config('app.name') }}
</x-mail::message>