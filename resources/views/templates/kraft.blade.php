<!DOCTYPE html>
<html class="no-js" lang="{{ App::getLocale() }}">
<head>
    @section('header')
        @include('templates/partials/meta')

        {{-- Polices du titre et du texte : chargées tôt, elles composent le haut de page --}}
        <link rel="preload" href="/fonts/happy-kids.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="/fonts/aileron-400.woff2" as="font" type="font/woff2" crossorigin>

        {{-- Tailwind, propre à cette page : les autres gardent Bootstrap (templates/layout) --}}
        @vite('resources/css/kraft.css')
    @show
</head>
<body>
    @section('skip')
        <a class="lien-evitement" href="#form">@lang('home.skip')</a>
    @show

    <div class="kt">
        <header class="kt-head">
            <div class="kt-head__inner">
                <a class="kt-logo" href="/"><img src="/images/logo-lockup.svg" alt="@lang('home.logo_alt')" width="190" height="70"></a>
                <nav class="kt-nav" aria-label="@lang('home.nav.label')">
                    @section('nav')
                        <a class="kt-navtext" href="#how">@lang('home.nav.how')</a>
                        <a class="kt-navtext" href="{{ route('faq') }}">@lang('home.nav.faq')</a>
                        <span class="tag-wrap tag-wrap--kraft"><a class="tag-kraft" href="#form" style="--hole: var(--color-creme-clair)">@lang('home.nav.go')</a></span>
                    @show
                </nav>
                <button type="button" class="kt-burger" aria-expanded="false" aria-controls="kt-menu" aria-label="@lang('home.nav.open')" data-label-open="@lang('home.nav.open')" data-label-close="@lang('home.nav.close')">
                    <svg aria-hidden="true" focusable="false" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                </button>
            </div>
            <nav id="kt-menu" class="kt-menu" aria-label="@lang('home.nav.label')" hidden>
                @section('menu')
                    <a href="#how">@lang('home.nav.how')</a>
                    <a href="{{ route('faq') }}">@lang('home.nav.faq')</a>
                    <a href="#form">@lang('home.nav.go')</a>
                @show
            </nav>
        </header>
        <div class="ruban ruban--fin" aria-hidden="true"></div>

        <main class="kt-main">
            @yield('body')
        </main>

        <footer class="kt-foot">
            <div class="kt-foot__festons" aria-hidden="true"></div>
            <div class="kt-foot__inner">
                <div class="kt-foot__haut">
                    <a class="kt-logo" href="#"><img src="/images/logo-lockup.svg" alt="@lang('home.logo_alt_top')" width="190" height="70"></a>
                    <nav class="kt-foot__liens" aria-label="@lang('home.footer.label')">
                        <a href="{{ url('/') }}#how">@lang('home.nav.how')</a>
                        <a href="{{ route('faq') }}">@lang('home.nav.faq')</a>
                        <a href="{{ route('legal') }}">@lang('home.footer.legal')</a>
                    </nav>
                    <div class="kt-foot__don">
                        <p>@lang('home.footer.free')</p>
                        @if (config('app.bmc'))
                            <span class="tag-wrap tag-wrap--kraft"><a class="tag-kraft" href="https://www.buymeacoffee.com/{{ config('app.bmc') }}" target="_blank" rel="noopener noreferrer" style="--hole: var(--color-creme-clair)"><svg aria-hidden="true" focusable="false" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 9h13v5a5 5 0 0 1-5 5H9a5 5 0 0 1-5-5z"/><path d="M17 11h1.5a2.5 2.5 0 0 1 0 5H17"/><path d="M8 3v3M12 3v3"/></svg>@lang('home.footer.coffee')</a></span>
                        @endif
                    </div>
                </div>
                <div class="kt-foot__bas">
                    <span>{!! __('home.footer.copyright', ['year' => date('Y'), 'author' => '<a href="https://www.korko.fr">Korko</a>']) !!}</span>
                    <span>{!! __('home.footer.source', ['link' => '<a href="https://framagit.org/Korko/SecretSanta">GitLab</a>', 'version' => e($version)]) !!}</span>
                </div>
            </div>
        </footer>
    </div>

    @section('script')
    @show
</body>
</html>
