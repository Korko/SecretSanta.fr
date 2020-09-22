<script>
    import jQuery from 'jquery';

    import axios from '../partials/axios.js';
    import store from '../partials/store.js';

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
            $v: {
                type: Object,
                default: null
            }
        },
        data: () => {
            return {
                ...store,
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

                    var data = null;
                    if(Array.isArray(options.data)) {
                        data = (options.data.length) ?
                            options.data.concat(
                                Object.keys(keys).map(key => {
                                    return { name: key, value: keys[key] };
                                })
                            ) : null;
                    } else {
                        data = Object.assign({}, options.data);
                    }

                    return axios({
                        url: url,
                        method: data ? 'POST' : 'GET',
                        data: data
                    })
                    .then(response => {
                        this.sending = false;
                        this.sent = true;

                        (options.success || options.then || function() {})(response.data);
                        (options.complete || options.finally || function() {})();

                        this.$emit('success', data);
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
            submit(postData, options) {
                this.$emit('beforeSubmit');
                postData = postData || jQuery(this.$el).serializeArray();
                var ajax = this.call(this.action, Object.assign({ data: postData }, options));
                this.$emit('afterSubmit');
                return ajax;
            }
        }
    };
</script>

<template>
    <form :action="action" method="post" autocomplete="off" @submit.prevent="onSubmit">
        <fieldset :disabled="sending || sent">
            <slot v-bind="{ sending, sent, submit, onSubmit, fieldError }" />
        </fieldset>
        <fieldset v-if="button">
            <button type="submit" class="btn btn-primary btn-lg">
                <span v-if="sent"><span class="fas fa-check-circle" /> {{ buttonSent || $t('common.form.sent') }}</span>
                <span v-else-if="sending"><span class="fas fa-spinner" /> {{ buttonSending || $t('common.form.sending') }}</span>
                <span v-else>{{ buttonSend || $t('common.form.send') }}</span>
            </button>
        </fieldset>
    </form>
</template>
