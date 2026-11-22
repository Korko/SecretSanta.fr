@extends('emails/layout_plain')

@section('title')
    {!! __('emails.dear_santa.heading', ['name' => $targetName]) !!}
@endsection

@section('content')
    > {!! $content !!}
@endsection
