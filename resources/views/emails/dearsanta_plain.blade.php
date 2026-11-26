@extends('emails/layout_plain')

@section('title')
{!! __('emails.dear_santa.heading', ['name' => $targetName]) !!}
@endsection

@section('content')
{!! __('emails.dear_santa.intro') !!}

{!! __('emails.gift.from') !!} : {!! $targetName !!}
> {!! $content !!}

{!! __('emails.dear_santa.you_too') !!}
{!! __('emails.dear_santa.write_yours') !!}

{!! __('emails.target_drawn.write_button') !!} : {!! $dearSantaLink !!}
@endsection
