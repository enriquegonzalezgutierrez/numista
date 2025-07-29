<x-mail::message>
# {{ __('mail.password_reset_subject') }}

{{ __('mail.password_reset_line_1') }}

<x-mail::button :url="$resetUrl">
{{ __('mail.password_reset_action') }}
</x-mail::button>

{{ __('mail.password_reset_expire', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]) }}

{{ __('mail.password_reset_line_2') }}

@lang('mail.regards'),<br>
{{ config('app.name') }}

@slot('subcopy')
<x-mail::subcopy>
@lang('mail.subcopy', ['actionText' => __('mail.password_reset_action'), 'actionUrl' => $resetUrl])
</x-mail::subcopy>
@endslot
</x-mail::message>