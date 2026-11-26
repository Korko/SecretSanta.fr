<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="{{ App::getLocale() }}"> <!--<![endif]-->
<head>
    @section('header')
        @include('templates/partials/meta')

        <!-- css -->
        @vite('resources/sass/vendor.scss')
        @vite('resources/sass/app.scss')
    @show
</head>
<body data-spy="scroll" data-target="#navbar" data-offset="120">
    <div id="loadOverlay" style="background-color:#FFF; position:absolute; top:0px; left:0px; width:100%; height:100%; z-index:2000;"></div>

    <span id="ribbon" class="d-none d-md-block"><a href="{{ route('faq') }}">@lang('form.nav.faq')</a></span>

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
    @show
</body>
</html>
