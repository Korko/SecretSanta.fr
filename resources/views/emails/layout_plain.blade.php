>> {{ __('emails.layout.automatic') }} <<

@yield('content')

---

{{ __('emails.layout.signature') }}

{{ config('app.name') }} - {{ config('app.url') }}
