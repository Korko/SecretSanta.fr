@extends('templates/layout', isset($styles) ? ['styles' => $styles] : [])

@section('content')
    <div id="form" v-cloak>
        <component :is="state" v-if="state === 'Form'" v-bind="$data">
            <template v-slot:default="$slot">
                @yield('form')
            </template>
        </component>
        <component :is="state" v-else v-on:success="send('success', $event)" v-on:error="send('failure', $event)" fetchurl="{{ $fetchUrl }}" v-bind="$data"></component>
    </div>
@stop

