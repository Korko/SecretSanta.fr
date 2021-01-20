@extends('emails/layout_plain')

@yield('title', "Message de {{ $targetName }}")

@section('content')
    > {{ $content }}
@endsection