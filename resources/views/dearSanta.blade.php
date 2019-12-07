@extends('templates/layout', ['styles' => '/css/dearSanta.css'])

@section('body')
    <div id="form" v-cloak>
        <component :is="state" v-if="state === 'DearSantaFetcher'" formurl="{{ route('dearsanta.fetch', ['santa' => $santa]) }}"></component>
        <component :is="state" v-else-if="state === 'DearSantaForm'" formurl="{{ route('dearsanta.contact', ['santa' => $santa]) }}"></component>
        <component :is="state" v-else></component>
    </div>
@stop

@section('script')
    @parent

    {!! NoCaptcha::renderJs(App::getLocale()) !!}

    <script type="text/javascript" src="{{ mix('/js/common.js') }}"></script>
    <script type="text/javascript" src="{{ mix('/js/dearSanta.js') }}"></script>
@stop
