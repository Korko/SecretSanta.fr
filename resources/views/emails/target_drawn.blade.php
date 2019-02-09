@extends('emails/layout')

@section('main')
    <p>{!! nl2br(htmlentities($content)) !!}</p>

    {!! nl2br( !empty($dearSantaLink) ? trans('form.mail.post2', ['link' => "<a href=\"$dearSantaLink\">$dearSantaLink</a>"]) : trans('form.mail.post') ) !!}
@endsection
