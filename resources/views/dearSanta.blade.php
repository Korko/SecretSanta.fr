@extends('templates/fetcher', ['styles' => '/css/dearSanta.css', 'fetchUrl' => route('dearsanta.fetch', ['santa' => $santa])])

@section('form')
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
@stop

@section('script')
    @parent

    {!! NoCaptcha::renderJs(App::getLocale()) !!}

    <script type="text/javascript" src="{{ mix('/js/common.js') }}"></script>
    <script type="text/javascript" src="{{ mix('/js/dearSanta.js') }}"></script>
@stop
