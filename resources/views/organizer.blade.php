@extends('templates/fetcher', ['styles' => '/css/organizer.css', 'fetchUrl' => route('organizerPanel.fetch', ['draw' => $draw])])

@section('form')
    <div>
        <table class="table table-hover">
            <thead>
                <tr class="table-active">
                    <th scope="col">@lang('organizer.list.name')</th>
                    <th scope="col">@lang('organizer.list.email')</th>
                    <th scope="col">@lang('organizer.list.status')</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(participant, idx) in data.participants">
                    <td>@{{ participant.name }}</td>
                    <td><input-edit v-model="participant.email_address" type="email"></input-edit></td>
                    <td>@{{ participant.delivery_status }}</td>
                </tr>
            </tbody>
        </table>
    </div>
@stop

@section('script')
    @parent

    {!! NoCaptcha::renderJs(App::getLocale()) !!}

    <script type="text/javascript" src="{{ mix('/js/common.js') }}"></script>
    <script type="text/javascript" src="{{ mix('/js/organizer.js') }}"></script>
@stop

