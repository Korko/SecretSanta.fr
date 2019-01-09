@extends('templates/layout', ['styles' => '/css/dearSanta.css'])

@section('header')
    @parent

    <!-- config js -->
    @javascript([
        'challenge' => $challenge,
        'text' => 'Ping?'
    ])
@stop

@section('body')
    <div id="form" v-cloak>
        <div v-if="!verified" class="alert alert-danger" role="alert">
            <span class="fas fa-exclamation-triangle" aria-hidden="true"></span>
            <span class="sr-only">Error:</span>
            This link is invalid, please try again
        </div>

        <form v-else action="{{ url('/dearsanta/'.$santa) }}" @submit.prevent="submit" method="post" autocomplete="off">
            <fieldset :disabled="sending || sent">
                <fieldset>
                    <div class="form-group">
                        <label for="mailTitle">Titre du mail</label>
                        <input id="mailTitle" type="text" name="title" required placeholder="Ma liste de Noël" value="" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label for="mailContent">Contenu du mail</label>
                        <textarea id="mailContent" name="content" required placeholder="Cher Papa Noël..." class="form-control"></textarea>
                    </div>
                </fieldset>
                <fieldset>
                    <div class="form-group btn">
                        {!! NoCaptcha::display(['data-theme' => 'light']) !!}
                    </div>

                    {{ csrf_field() }}
                    <input type="hidden" name="key" :value="key" />
                    <button type="submit" class="btn btn-primary btn-lg">
                        <span v-if="sent"><span class="fas fa-check-circle"></span> @lang('form.sent')</span>
                        <span v-else-if="sending"><span class="fas fa-spinner"></span> @lang('form.sending')</span>
                        <span v-else>Envoyer</span>
                    </button>
                </fieldset>
            </fieldset>
        </form>
    </div>
@stop

@section('script')
    @parent

    {!! NoCaptcha::renderJs(App::getLocale()) !!}

    <script type="text/javascript" src="{{ mix('/js/common.js') }}"></script>
    <script type="text/javascript" src="{{ mix('/js/dearSanta.js') }}"></script>
@stop
