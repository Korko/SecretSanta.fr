<template>
    <form
        :action="`/dearsanta/${data.id}/send`"
        @submit.prevent="submit"
        method="post"
        autocomplete="off"
    >
        <input type="hidden" name="_token" :value="csrf" />
        <fieldset :disabled="sending || sent">
            <fieldset>
                <div class="form-group">
                    <label for="mailContent">Contenu du mail</label>
                    <textarea
                        id="mailContent"
                        name="content"
                        required
                        placeholder="Cher Papa Noël..."
                        class="form-control"
                    ></textarea>
                </div>
            </fieldset>
            <fieldset>
                <div class="form-group btn">
                    <!-- {!! NoCaptcha::display(['data-theme' => 'light']) !!} -->
                </div>

                <input type="hidden" name="key" :value="key" />
                <button type="submit" class="btn btn-primary btn-lg">
                    <span v-if="sent"
                        ><span class="fas fa-check-circle"></span>
                        @lang('form.sent')</span
                    >
                    <span v-else-if="sending"
                        ><span class="fas fa-spinner"></span>
                        @lang('form.sending')</span
                    >
                    <span v-else>Envoyer</span>
                </button>
            </fieldset>
        </fieldset>
    </form>
</template>

<script>
    import { mapState } from 'vuex';

    import Lang from '../partials/lang.js';

    import DefaultForm from './form.vue';

    export default {
        extends: DefaultForm,
        computed: mapState(['lang'])
    };
</script>
