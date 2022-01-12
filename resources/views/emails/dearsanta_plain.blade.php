@extends('emails/layout_plain', ['title' => 'dear_santa'])

@section('content')
    Message de {{ $targetName }}

    > {{ $content }}
@endsection