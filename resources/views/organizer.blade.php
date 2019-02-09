@extends('templates/layout', ['styles' => '/css/organizer.css'])

@section('header')
    @parent

    <!-- config js -->
    @javascript([
        'challenge' => $challenge,
        'text' => config('app.challenge'),
        'participants' => $participants,
    ])
@stop

@section('body')
    <div id="form" v-cloak>
        <div v-if="!verified" class="alert alert-danger" role="alert">
            <span class="fas fa-exclamation-triangle" aria-hidden="true"></span>
            <span class="sr-only">Error:</span>
            This link is invalid, please try again
        </div>
        <div v-else>
            <table>
                <tr v-for="participant in participants">
                    <td>@{{ participant.name }}</td>
                    <td>@{{ participant.email_address }}</td>
                    <td>@{{ participant.delivery_status }}</td>
                </tr>
            </table>
        </div>
    </div>
@stop

@section('script')
    @parent

    {!! NoCaptcha::renderJs(App::getLocale()) !!}

    <script type="text/javascript" src="{{ mix('/js/common.js') }}"></script>
    <script type="text/javascript" src="{{ mix('/js/organizer.js') }}"></script>
@stop

