@extends('templates/layout', ['styles' => '/assets/dearSanta.css'])

@section('header')
    @parent

    <!-- config js -->
    @javascript([
        'challenge' => $challenge,
        'iv' => $iv,
        'text' => 'Ping?'
    ])
@stop

@section('body')
    <div id="form" v-cloak>
        <div class="alert alert-danger" role="alert" v-if="!verified">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <span class="sr-only">Error:</span>
            This link is invalid, please try again
        </div>
        <form v-else>
            <fieldset :disabled="!verified">
                <input type="text" name="title" />
                <textarea name="content"></textarea>
            </fieldset>
        </form>
    </div>
@stop

@section('script')
    <script type="text/javascript" src="{{ URL::asset('/assets/dearSanta.js') }}"></script>
@stop
