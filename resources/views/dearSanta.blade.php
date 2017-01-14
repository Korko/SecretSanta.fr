@extends('templates/layout', ['styles' => '/assets/dearSanta.css'])

@section('header')
    @parent

    <!-- config js -->
    @javascript([
        'challenge' => $challenge,
        'iv' => $iv,
        'text' => 'Ping?'
    ])
@stop

@section('body')
    <div id="form" v-cloak>
        <div v-if="!verified" class="alert alert-danger" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <span class="sr-only">Error:</span>
            This link is invalid, please try again
        </div>
        <form v-else action="{{ url('/dearsanta/'.$santa) }}" @submit.prevent="submit" method="post" autocomplete="off">
            <fieldset>
                <div class="form-group">
                    <label for="mailTitle">Titre du mail</label>
                    <input id="mailTitle" type="text" name="title" required placeholder="Ma liste de Noël" value="" class="form-control" />
                </div>
                <div class="form-group">
                    <label for="mailContent">Contenu du mail</label>
                    <textarea id="mailContent" name="content" required placeholder="Cher Papa Noël..." class="form-control"></textarea>
                </div>
                <input type="button" />
            </fieldset>
            <fieldset>
                <div class="form-group btn">
                    {!! Recaptcha::renderElement(['data-theme' => 'light']) !!}
                </div>

                {{ csrf_field() }}
                <input type="hidden" name="key" :value="key" />
                <button type="submit" class="btn btn-primary btn-lg">
                    <span v-if="sending"><span class="glyphicon glyphicon-refresh spinning"></span> @lang('form.sending')</span>
                    <span v-if="sent"><span class="glyphicon glyphicon-ok"></span> @lang('form.sent')</span>
                    <span v-else>Envoyer</span>
                </button>
            </fieldset>
        </form>
    </div>
@stop

@section('script')
    {!! Recaptcha::renderScript(App::getLocale()) !!}

    <script type="text/javascript" src="{{ URL::asset('/assets/dearSanta.js') }}"></script>
@stop
