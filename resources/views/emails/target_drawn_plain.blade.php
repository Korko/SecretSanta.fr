@extends('emails/layout_plain', ['title' => 'target_drawn'])

@section('content')
    > {{ $content }}

    ---

    Vous pouvez écrire à votre Père Noël Secret ! Ca se passe ici : {{ $dearSantaLink }}
@endsection