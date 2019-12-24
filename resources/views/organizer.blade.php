@extends('templates/fetcher', ['styles' => '/css/organizer.css', 'fetchUrl' => route('organizerPanel.fetch', ['draw' => $draw])])

@section('script')
    @parent

    {!! NoCaptcha::renderJs(App::getLocale()) !!}

    <script type="text/javascript" src="{{ mix('/js/common.js') }}"></script>
    <script type="text/javascript" src="{{ mix('/js/organizer.js') }}"></script>
@stop

