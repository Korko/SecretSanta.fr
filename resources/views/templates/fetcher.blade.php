@extends('templates/layout', isset($styles) ? ['styles' => $styles] : [])

@section('body')
    <template id="error-template">
      <span>Une erreur est survenue</span>
    </template>
    <template id="fetcher-template">
        <form id="fetch" action="{{ $fetchUrl }}" @submit.prevent="submit" method="post" autocomplete="off">
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
        <div>@yield('form')</div>
    </template>

    <div id="form" v-cloak>
        <component :is="state" v-on:success="stateSuccess" v-on:error="stateFailure"></component>
    </div>
@stop

