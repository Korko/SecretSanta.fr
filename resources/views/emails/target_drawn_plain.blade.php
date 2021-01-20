@extends('emails/layout_plain')

@yield('title', "Message aux participants")

@section('content')
    > {{ $content }}

    ---

    Vous pouvez écrire à votre Père Noël Secret ! Ca se passe ici : {{ $dearSantaLink }}
@endsection