@extends('templates/kraft')

@section('header')
    @parent

    @javascript([
        'alert' => $limitInvalid ? __('error.participantLimit') : session('message', ''),
        'maxParticipants' => $maxParticipants,
        'limitCode' => $limitCode,
        'contact' => config()->string('app.email'),
    ])
@stop

@section('body')
    {{-- Haut de page : sur ordinateur la photo en fond sous un voile ; sur mobile le texte, la carte, puis la photo --}}
    <section class="hero">
        <div class="kt-veil" aria-hidden="true"></div>
        <div class="hero__inner">
            <div class="hero__col">
                <p class="mentions">
                    @foreach (__('home.hero.mentions') as $mention)
                        @unless ($loop->first)<span aria-hidden="true">·</span>@endunless
                        <span>{{ $mention }}</span>
                    @endforeach
                </p>
                <h1 class="hero__titre">
                    <span class="insecable">{{ __('home.hero.title_1') }}@if (__('home.hero.title_join') === '-')<span class="tiret-offrez">-</span>@else{{ ' ' }}@endif{{ __('home.hero.title_2') }}</span>
                    {{ __('home.hero.title_3') }}
                    <span class="hero__accent">{{ __('home.hero.title_accent') }}</span>
                </h1>
                <p class="hero__intro">{{ __('home.hero.intro') }}</p>
                <div class="carte-etiquette-wrap">
                    <div class="kt-card carte-etiquette">
                        <span class="kt-hole" aria-hidden="true"></span>
                        @include('partials/illustrations/ficelle-boucle')
                        <label class="carte-etiquette__label" for="hero-title">@lang('home.hero.event_label')</label>
                        <div class="carte-etiquette__ligne">
                            <input id="hero-title" class="field field--haut" type="text" autocomplete="off" placeholder="@lang('home.hero.event_placeholder')">
                            <span class="tag-wrap"><a class="tag-action" href="#form" data-focus-form>@lang('home.hero.go')</a></span>
                        </div>
                        <p class="carte-etiquette__note">@lang('home.hero.no_account')</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="hero__photo" role="img" aria-label="@lang('home.hero.photo_alt')"></div>
    </section>

    <div class="ruban ruban--noeud" aria-hidden="true"><div class="noeud">@include('partials/illustrations/noeud-ruban')</div></div>

    <section id="how" class="etapes" aria-labelledby="how-title">
        <div class="etapes__inner">
            <h2 id="how-title" class="titre etapes__titre">@lang('home.steps.title_before') <span class="titre-accent">@lang('home.steps.title_accent')</span>@lang('home.steps.title_after')</h2>
            <ol class="kt-steps">
                @foreach ([['one', 'red', 'etape-1-participants'], ['two', 'green', 'etape-2-message'], ['three', 'gold', 'etape-3-jour-j']] as [$step, $color, $art])
                    <li class="kt-step">
                        <div class="kt-stepart">
                            @include('partials/illustrations/'.$art)
                            <span class="sticker sticker--{{ $color }} sticker--48" aria-hidden="true">{{ $loop->iteration }}</span>
                        </div>
                        <h3 class="kt-step__titre">@lang('home.steps.'.$step.'_title')</h3>
                        <p class="kt-step__texte">@lang('home.steps.'.$step.'_text')</p>
                    </li>
                @endforeach
            </ol>

            <div class="secret-box">
                <span class="secret-box__icone" aria-hidden="true"><svg focusable="false" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/></svg></span>
                <p class="secret-box__texte"><strong>@lang('home.secret.strong')</strong> @lang('home.secret.text')</p>
            </div>

            <div class="apres">
                <div class="apres__dessin">@include('partials/illustrations/et-apres-lettre')</div>
                <div class="apres__texte">
                    <h3 class="titre apres__titre">@lang('home.after.title')</h3>
                    <p>@lang('home.after.text_1')</p>
                    <p>@lang('home.after.text_2')</p>
                </div>
            </div>
        </div>
    </section>

    <section class="bandeau-photo" aria-label="@lang('home.band.label')">
        <div class="bandeau-photo__voile">
            {{-- L'apostrophe de Happy Kids est mal dessinée : celle-ci est composée en Aileron --}}
            <p class="bandeau-photo__etiquette">@lang('home.band.text_before')@if (__('home.band.text_after') !== '')<span class="apos">’</span>@lang('home.band.text_after')@endif</p>
        </div>
    </section>

    <section id="form" class="k-form kraft" aria-labelledby="form-title">
        <div class="k-form__inner">
            <div class="k-form__entete">
                <h2 id="form-title" class="titre k-form__titre">@lang('home.go.title_before') <span class="titre-accent">@lang('home.go.title_accent')</span></h2>
                <p class="k-form__intro">@lang('home.go.intro')</p>
            </div>
            <div id="random-form">
                <p class="k-attente">@lang('home.go.waiting', ['email' => config()->string('app.email')])</p>
            </div>
        </div>
    </section>
@stop

@section('script')
    @parent

    @vite('resources/js/common.js')
    @vite('resources/js/randomForm.js')
@stop
