@extends('templates/layout', isset($styles) ? ['styles' => $styles] : [])

@section('header')
    @parent

    <!-- config js -->
    @javascript([
        'api'   => config('captcha.sitekey'),
    ])
@stop

@section('body')
    <div id="form" v-cloak>
        <component :is="state" v-if="state === 'Form'" :data="data">
            <template v-slot:default="$slot">
                @yield('form')
            </template>
        </component>
        <component :is="state" v-else v-on:success="send('success', $event)" v-on:error="send('failure', $event)" fetchurl="{{ $fetchUrl }}" :data="data"></component>
    </div>
@stop

