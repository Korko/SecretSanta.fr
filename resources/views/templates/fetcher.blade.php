@extends('templates/layout', isset($styles) ? ['styles' => $styles] : [])

@section('content')
    <div id="form" data-url="{{ $fetchUrl }}"></div>
@stop

