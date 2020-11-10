<script>
    import jQuery from 'jquery';

    import axios from '../partials/axios.js';

    export default {
        props: {
            action: {
                type: String,
                default: ''
            },
            button: {
                type: Boolean,
                default: true
            },
            buttonSend: {
                type: String,
                default: ''
            },
            buttonSending: {
                type: String,
                default: ''
            },
            buttonSent: {
                type: String,
                default: ''
            },
            buttonReset: {
                type: String,
                default: ''
            },
            $v: {
                type: Object,
                default: null
            },
            sendIcon: {
                type: String,
                default: 'paper-plane'
            },
            autoReset: {
                type: Boolean,
                default: false
            }
        },
        data: () => {
            return {
                fieldErrors: [],
                sending: false,
                sent: false
            };
        },
        watch: {
            sending() {
                this.$emit('change', this.sending);
            }
        },
        methods: {
            fieldError(field) {
                return this.fieldErrors[field] ? this.fieldErrors[field][0] : null;
            },
            call(url, options) {
                if (!this.sending && !this.sent) {
                    this.$v && this.$v.$touch();

                    if (this.$v && this.$v.$invalid) {
                        return false;
                    }

                    this.sending = true;

                    return axios
                        .post(url, options.data)
                        .then(response => {
                            this.fieldErrors = [];
                            this.sending = false;

                            if(!this.autoReset) {
                                this.sent = true;
                            }

                            (options.success || options.then || function() {})(response.data);
                            (options.complete || options.finally || function() {})();

                            this.$emit('success', response.data);

                            if(this.autoReset) {
                                this.onReset();
                            }
                        })
                        .catch(error => {
                            if (error.response.data && error.response.data.errors)
                                this.fieldErrors = error.response.data.errors;

                            this.sending = false;

                            (options.error || options.catch || function() {})(error.response.data);
                            (options.complete || options.finally || function() {})();

                            this.$emit('error');
                        });
                }
            },
            onSubmit() {
                this.submit();
            },
            onReset() {
                this.$emit('reset');
                this.fieldErrors = [];
                this.$v.$reset();
                this.sending = false;
                this.sent = false;
            },
            submit(postData, options) {
                this.$emit('beforeSubmit');
                postData = postData || jQuery(this.$el).serialize();
                var ajax = this.call(this.action, Object.assign({ data: postData }, options));
                this.$emit('afterSubmit');
                return ajax;
            }
        }
    };
</script>

<template>
    <form :action="action" method="post" autocomplete="off" @submit.prevent="onSubmit" @reset.prevent="onReset">
        <fieldset :disabled="sending || sent">
            <slot v-bind="{ sending, sent, submit, onSubmit, onReset, fieldError }" />
        </fieldset>
        <fieldset v-if="button">
            <button type="submit" class="btn btn-primary btn-lg" :disabled="sent || sending">
                <span v-if="sent"><span class="fas fa-check-circle" /> {{ buttonSent || $t('common.form.sent') }}</span>
                <span v-else-if="sending"><span class="fas fa-spinner" /> {{ buttonSending || $t('common.form.sending') }}</span>
                <span v-else><span :class="'fas fa-'+sendIcon" /> {{ buttonSend || $t('common.form.send') }}</span>
            </button>
            <button v-if="sent" type="reset" class="btn btn-primary btn-lg">
                <span><span class="fas fa-backward" /> {{ buttonReset || $t('common.form.reset') }}</span>
            </button>
        </fieldset>
    </form>
</template>
