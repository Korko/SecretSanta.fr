@extends('emails/layout_plain')

@section('title')
    Message aux participants
@endsection

@section('content')
    > {{ $content }}

    ---

    Vous pouvez écrire à votre Père Noël Secret ! Ca se passe ici : {{ $dearSantaLink }}
@endsection