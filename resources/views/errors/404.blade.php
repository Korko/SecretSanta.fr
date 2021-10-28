@extends('templates/layout')

@section('content')
    <div class="polaroid-wrapper">

        <div class="item">
            <div class="polaroid">
                <div class="caption"></div>
            </div>
            <p class="wasted">Wasted</p>
        </div>

    </div>
    <div class="error-content">

        <h1 class="section-title text-center">@lang('404.title')</h1>
        @hasSection('subtitle')<p class="lead text-center">@yield('subtitle')</p>@endif

        <p class="back text-center"><a href="/" class="btn btn-primary">Revenir sur la page d'accueil</a></p>

    </div>
@stop

@section('script')
    @parent

    <script type="text/javascript" src="{{ mix('/js/common.js') }}"></script>
    <script type="text/javascript" src="{{ mix('/js/app.js') }}"></script>
@stop