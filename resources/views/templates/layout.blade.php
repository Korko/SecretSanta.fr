<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="{{ App::getLocale() }}"> <!--<![endif]-->
<head>
    @section('header')
        <title>{{ __('headers.title') }}</title>

        <link rel="canonical" href="{{ url('/') }}">

        <!-- meta -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta http-equiv="content-language" content="{{ App::getLocale() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="{{ __('headers.description') }}">
        <meta name="keywords" content="{{ __('headers.keywords') }}">
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
        <meta property="og:description" content="{{ __('headers.description') }}">
        <meta property="og:type" content="website">
        <meta property="og:locale" content="{{ App::getLocale() }}">

        <!-- twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="@korkof">
        <meta name="twitter:creator" content="@korkof">
        <meta name="twitter:title" content="SecretSanta.fr">
        <meta name="twitter:description" content="{{ __('headers.description') }}">
        <meta name="twitter:image" content="{{ url('/images/opengraph_banner_16_9.png') }}">
        <meta name="twitter:image" content="{{ __('headers.logo_alt') }}">
        <meta name="twitter:url" content="{{ url('/') }}">

        <!-- facebook image -->
        <link rel="image_src" href="{{ url('/images/opengraph_banner_19.1_1.png') }}" />

        <!-- css -->
        @vite('resources/css/vendor.css')
        @vite('resources/css/app.css')
    @show
</head>
<body data-spy="scroll" data-target="#navbar" data-offset="120">
    <div id="loadOverlay" style="background-color:#FFF; position:absolute; top:0px; left:0px; width:100%; height:100%; z-index:2000;"></div>

    <span id="ribbon" class="d-none d-md-block"><a href="{{ route('faq') }}">{{ __('form.nav.faq') }}</a></span>

    <div id="wrap">
        <div id="main">
            <div id="menu" class="navbar navbar-dark fixed-top navbar-expand-md" role="navigation">
                <nav id="navbar">
                    <div id="logo">
                        <a href="/"></a>
                    </div>
                    @yield('navbar')
                </nav><!--/.navbar-collapse -->
            </div><!-- menu -->

            @section('body')
                <div id="content">
                    @yield('content')
                </div>
            @show
        </div>
    </div>

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

    @section('script')
        <script src="https://use.fontawesome.com/releases/v5.6.3/js/all.js" data-auto-replace-svg="nest"></script>
        @vite('resources/js/manifest.js')
        @vite('resources/js/vendor.js')
    @show
</body>
</html>
