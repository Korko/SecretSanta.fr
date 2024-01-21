<script>
    import { post } from '@/Modules/fetch.js';

    import { useForm } from 'laravel-precognition-vue';

    export default {
        props: {
            action: {
                type: String,
                default: ''
            },
            method: {
                type: String,
                default: 'post'
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
            },
            isInvalid: {
                type: Boolean,
                default: false
            },
            formData: {
                type: Object,
                default: () => ({})
            }
        },
        data: () => ({
            form: useForm(this.method || 'post', this.action, this.formData || {}),
            sending: false,
            sent: false
        }),
        watch: {
            formData() {
                this.form.setData(this.formData);
            },
            sending() {
                this.$emit('change', this.sending);
            }
        },
        methods: {
            call(url, options) {
                if (!this.sending && !this.sent && !this.isInvalid) {
                    this.sending = true;

                    return post(url, options.data)
                        .then(response => {
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
                            this.sending = false;

                            var callback;
                            if(callback = (options.error || options.catch)) {
                                callback(response);
                            }

                            var callback2;
                            if(callback2 = (options.complete || options.finally)) {
                                callback2();
                            }

                            if(!callback && !callback2 && response?.errors.length > 0) {
                                this.$dialog
                                    .alert(this.$t('form.internalError'));
                            } else if(response.message) {
                                this.$dialog
                                    .alert(response.message);
                            }

                            this.$emit('error', response?.errors);
                        });
                }
            },
            onSubmit() {
                this.submit();
            },
            onReset() {
                this.$emit('reset');
                this.sending = false;
                this.sent = false;
            },
            submit(postData, options) {
                this.$emit('beforeSubmit');

                //var ajax = this.call(this.action, Object.assign({ data: postData }, options));
                this.$emit('afterSubmit');
                return ajax;
            }
        }
    };
</script>

<template>
    <form :action="action" method="post" autocomplete="off" @submit.prevent="onSubmit" @reset.prevent="onReset">
        <fieldset :disabled="sending || sent">
            <slot v-bind="{ form, sending, sent, submit, onSubmit, onReset }" />
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
