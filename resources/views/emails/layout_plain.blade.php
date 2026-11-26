{{-- Text part, never read as HTML: {!! !!} in every _plain view, {{ }} would show entities like &#039; --}}
@yield('title')

@yield('content')

---

{!! __('emails.layout.signature') !!}
{!! config('app.name') !!} - {!! config('app.url') !!}

{!! __('emails.layout.automatic') !!}
{!! config('app.name') !!} · {!! __('emails.layout.tagline') !!}
