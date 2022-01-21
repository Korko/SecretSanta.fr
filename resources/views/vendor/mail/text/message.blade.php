@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            {{ config('app.name') }}
        @endcomponent
    @endslot

    <p style="border:1px solid #000000;background-color:#dddddd;">Ceci est un message automatique, merci de ne pas y répondre.</p>

    {{-- Body --}}
    {{ $slot }}

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
            <p style="padding-bottom:10px !important">Secrètement votre,</p>

            <p><a href="{{ config('app.url') }}">{{ config('app.name') }}</a></p>
        @endcomponent
    @endslot
@endcomponent
