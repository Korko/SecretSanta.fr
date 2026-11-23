>> {!! __('emails.layout.automatic') !!} <<

@yield('content')

---

{!! __('emails.layout.signature') !!}

{!! config('app.name') !!} - {!! config('app.url') !!}
{{-- Text part, never read as HTML: {!! !!} in every _plain view, {{ }} would show entities like &#039; --}}
