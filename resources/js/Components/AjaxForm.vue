<script>
    import { post, precog } from '@/Modules/fetch.js';

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
            sendIcon: {
                type: String,
                default: 'paper-plane'
            },
            autoReset: {
                type: Boolean,
                default: false
            }
        },
        data: () => ({
            fieldErrors: [],
            sending: false,
            sent: false
        }),
        watch: {
            sending() {
                this.$emit('change', this.sending);
            }
        },
        methods: {
            fieldError(field) {
                return this.fieldErrors[field] ? this.fieldErrors[field][0] : null;
            },
            precog(url, data) {
                return precog(url, data)
                    .then(() => {
                        // Remove data keys from fieldErrors as they were validated
                        this.fieldErrors = Object.keys(this.fieldErrors)
                            .filter((key) => data[key])
                            .reduce((obj, key) => {
                                return Object.assign(obj, {
                                    [key]: this.fieldErrors[key]
                                });
                            }, {});
                    })
                    .catch(response => {
                        if (response && response.errors)
                            this.fieldErrors = Object.assign(this.fieldErrors, response.errors);
                    });
            },
            call(url, options) {
                if (!this.sending && !this.sent) {
                    this.sending = true;

                    return post(url, options.data)
                        .then(response => {
                            this.fieldErrors = {};
                            this.sending = false;

                            if(!this.autoReset) {
                                this.sent = true;
                            }

                            (options.success || options.then || function() {})(response);
                            (options.complete || options.finally || function() {})();

                            this.$emit('success', response);

                            if(this.autoReset) {
                                this.onReset();
                            }
                        })
                        .catch(response => {
                            if (response && response.errors)
                                this.fieldErrors = response.errors;

                            this.sending = false;

                            var callback;
                            if(callback = (options.error || options.catch)) {
                                callback(response);
                            }

                            var callback2;
                            if(callback2 = (options.complete || options.finally)) {
                                callback2();
                            }

                            if(!callback && !callback2 && this.fieldErrors.length > 0) {
                                this.$dialog
                                    .alert(this.$t('form.internalError'));
                            }

                            this.$emit('error');
                        });
                }
            },
            onSubmit() {
                this.submit();
            },
            onReset() {
                this.$emit('reset');
                this.fieldErrors = {};
                this.sending = false;
                this.sent = false;
            },
            validate(field, value) {
                return this.precog(this.action, {[field]: value});
            },
            submit(postData, options) {
                this.$emit('beforeSubmit');
                postData = postData || new URLSearchParams(new FormData(this.$el)).toString();
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
