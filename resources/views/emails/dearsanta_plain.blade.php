@extends('emails/layout_plain')

@section('title')
    Message de {{ $targetName }}
@endsection

@section('content')
    > {{ $content }}
@endsection