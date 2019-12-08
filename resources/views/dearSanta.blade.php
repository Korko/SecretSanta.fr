@extends('templates/layout', ['styles' => '/css/dearSanta.css'])

@section('body')
    <template id="error-template">
      <span>Une erreur est survenue</span>
    </template>
    <template id="fetcher-template">
        <form id="fetch" action="{{ route('dearsanta.fetch', ['santa' => $santa]) }}" @submit.prevent="submit" method="post" autocomplete="off">
            <input type="hidden" name="_token" :value="csrf">
            <input type="hidden" name="key" :value="key">
            <timer :delay="2000">
                <button type="submit" class="btn btn-primary btn-lg" :disabled="loading">
                    <span v-if="loading"><span class="fas fa-spinner"></span> Chargement en cours...</span>
                    <span v-else>Charger</span>
                </button>
            </timer>
        </form>
    </template>
    <template id="form-template">
        <form action="{{ route('dearsanta.contact', ['santa' => $santa]) }}" @submit.prevent="submit" method="post" autocomplete="off">
            <input type="hidden" name="_token" :value="csrf">
            <fieldset :disabled="sending || sent">
                <fieldset>
                    <div class="form-group">
                        <label for="mailContent">Contenu du mail</label>
                        <textarea id="mailContent" name="content" required placeholder="Cher Papa Noël..." class="form-control"></textarea>
                    </div>
                </fieldset>
                <fieldset>
                    <div class="form-group btn">
                        {!! NoCaptcha::display(['data-theme' => 'light']) !!}
                    </div>

                    <input type="hidden" name="key" :value="key" />
                    <button type="submit" class="btn btn-primary btn-lg">
                        <span v-if="sent"><span class="fas fa-check-circle"></span> @lang('form.sent')</span>
                        <span v-else-if="sending"><span class="fas fa-spinner"></span> @lang('form.sending')</span>
                        <span v-else>Envoyer</span>
                    </button>
                </fieldset>
            </fieldset>
        </form>
    </template>

    <div id="form" v-cloak>
        <component :is="state" v-on:success="stateSuccess" v-on:error="stateFailure"></component>
    </div>
@stop

@section('script')
    @parent

    {!! NoCaptcha::renderJs(App::getLocale()) !!}

    <script type="text/javascript" src="{{ mix('/js/common.js') }}"></script>
    <script type="text/javascript" src="{{ mix('/js/dearSanta.js') }}"></script>
@stop
