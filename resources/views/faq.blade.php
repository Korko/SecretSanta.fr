@extends('templates/kraftPage')

@section('title', __('home.nav.faq'))

@section('content')
    @foreach ($questions as $category => $qnas)
        <section class="pg-card">
            <h2 class="pg-h2">@lang("faq.categories.$category")</h2>
            @foreach ($qnas as $question => $answer)
                <details class="pg-details pg-qna">
                    <summary @if ($loop->parent->first) id="question{{ $loop->index }}" @endif class="pg-h3 pg-h3--sm">{{ $question }}</summary>
                    <p @if ($loop->parent->first) id="answer{{ $loop->index }}" @endif class="pg-p">{{ $answer }}</p>
                </details>
            @endforeach
        </section>
    @endforeach

    <section class="pg-card pg-card--row pg-tagrow">
        <p class="pg-p">@lang('faq.nav.contact')</p>
        <span class="tag-wrap"><a class="tag-action tag-action--petit" href="mailto:{{ config('app.email') }}">{{ config('app.email') }}</a></span>
    </section>
@stop
