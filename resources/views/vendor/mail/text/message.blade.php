@component('mail::layout')
    @component('mail::panel')
        {{ __('Ceci est un message automatique, merci de ne pas y répondre.') }}
    @endcomponent

    {{-- Body --}}
    {{ Illuminate\Mail\Markdown::parse($slot) }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            {{ __('Secrètement votre,') }}

            {{ config('app.name') }} - {{ config('app.url') }}
        @endcomponent
    @endslot
@endcomponent