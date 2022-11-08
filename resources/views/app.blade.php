<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
    <head>
        <link rel="canonical" href="{{ url('/') }}">

        <!-- meta -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta http-equiv="content-language" content="{{ App::getLocale() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <meta name="description" content="@lang('headers.description')">
        <meta name="keywords" content="@lang('headers.keywords')">
        <meta name="author" content="Korko <webmaster@secretsanta.fr>">
        <meta name=”robots” content="index, follow">
        <meta name="rating" content="safe for kids">

        <!-- opengraph/facebook -->
        <meta property="og:title" content="SecretSanta.fr">
        <meta property="og:site_name" content="SecretSanta.fr">
        <meta property="og:url" content="{{ url('/') }}">
        <meta property="og:image" content="{{ url('/images/opengraph_banner_19.1_1.png') }}">
        <meta property="og:image:url" content="{{ url('/images/opengraph_banner_19.1_1.png') }}">
        <meta property="og:image:alt" content="{{ url('/images/santaclaus.png') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:description" content="@lang('headers.description')">
        <meta property="og:type" content="website">
        <meta property="og:locale" content="{{ App::getLocale() }}">

        <!-- twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="@korkof">
        <meta name="twitter:creator" content="@korkof">
        <meta name="twitter:title" content="SecretSanta.fr">
        <meta name="twitter:description" content="@lang('headers.description')">
        <meta name="twitter:image" content="{{ url('/images/opengraph_banner_16_9.png') }}">
        <meta name="twitter:image" content="@lang('headers.logo_alt')">
        <meta name="twitter:url" content="{{ url('/') }}">

        <!-- facebook image -->
        <link rel="image_src" href="{{ url('/images/opengraph_banner_19.1_1.png') }}" />

        @routes

        <!-- media -->
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
        <!--<link
            rel="stylesheet"
            href="{{ Vite::asset('resources/sass/app.scss') }}"
            integrity=""
            crossorigin="anonymous"
        />
        <script
            type="text/javascript"
            src="{{ Vite::asset('resources/js/app.js') }}"
            integrity=""
            defer
        ></script>-->

        @inertiaHead
    </head>
    <body>
        <span id="ribbon" class="d-none d-md-block"><a href="{{ route('faq') }}">@lang('common.nav.faq')</a></span>

        @inertia

        <noscript>
            {{ trans('app.nojs') }}
        </noscript>

        <footer id="footer" class="dark-wrapper">
            <section class="ss-style-top"></section>
            <div class="container inner">
                <div class="row">
                    <div class="col-md-6">
                        @section('copyright')
                            &copy; Copyright SecretSanta.fr 2015
                            <br />{!! trans('footer.project', ['author' => '<a class="themeBy" href="https://www.korko.fr">Korko</a>']) !!}
                            <br />{!! trans('footer.fork', ['git' => '<a class="themeBy" href="https://framagit.org/Korko/SecretSanta">GitLab</a>']) !!}
                            <br />{!! trans('footer.version', ['version' => $version]) !!}
                        @show
                    </div>
                    <div class="col-md-6">
                        @section('copyright2')
                            {!! trans('footer.theme', ['author' => '<a class="themeBy" href="https://www.themewagon.com">ThemeWagon</a>']) !!}
                            <br />{!! trans('footer.icons', ['author' => '<a class="themeBy" href="https://www.iconfinder.com/iconsets/doublejdesign-free-icon-handy_color">Double-J Designs</a>']) !!}
                            <br /><a class="themeBy" href="{{ route('legal') }}">{{ trans('footer.legal') }}</a>
                        @show
                    </div>
                </div>
            </div>
            <!-- /.container -->
        </footer>

        @javascript([
            'pusher' => [
                'key' => Arr::get(config('websockets.apps'), '0.key'),
                'host' => Arr::get(config('websockets.apps'), '0.host'),
                'port' => intval(Arr::get(config('websockets.apps'), '0.port', 443)),
            ]
        ])
    </body>
</html>
