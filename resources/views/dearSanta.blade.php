@extends('templates/fetcher', ['styles' => '/css/dearSanta.css', 'fetchUrl' => route('dearsanta.fetch', ['participant' => $participant])])

@section('script')
    @parent

    <script type="text/javascript" src="{{ mix('/js/common.js') }}"></script>
    <script type="text/javascript" src="{{ mix('/js/dearSanta.js') }}"></script>
@stop
