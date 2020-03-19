<script>
    import { mapState } from 'vuex';
    import $ from 'jquery';
    import alertify from 'alertify.js';

    export default {
        props: {
            action: {
                type: String,
                default: ''
            },
            button: {
                type: Boolean,
                default: true
            }
        },
        data: () => {
            return {
                fieldErrors: [],
                sending: false,
                sent: false
            };
        },
        computed: {
            errors() {
                var errors = [];
                for (var field in this.fieldErrors) {
                    errors = errors.concat(this.fieldErrors[field]);
                }
                return errors;
            },
            ...mapState(['csrf', 'key', 'lang'])
        },
        watch: {
            sending() {
                this.$emit('change', this.sending);
            }
        },
        methods: {
            call(url, options) {
                if (!this.sending && !this.sent) {
                    this.sending = true;
                    var keys = { _token: this.csrf, key: this.key };
                    var app = this;
                    return $.ajax({
                        url: url,
                        type: options.data ? 'POST' : 'GET',
                        data: Array.isArray(options.data)
                            ? options.data.concat(
                                  Object.keys(keys).map(key => {
                                      return { name: key, value: keys[key] };
                                  })
                              )
                            : Object.assign({}, options.data, keys),
                        success(data, textStatus, jqXHR) {
                            if (
                                jqXHR.responseJSON &&
                                jqXHR.responseJSON.message
                            )
                                alertify.success(jqXHR.responseJSON.message);

                            app.sending = false;
                            app.sent = true;

                            (options.success || options.then || function() {})(
                                jqXHR.responseJSON
                            );
                            (
                                options.complete ||
                                options.finally ||
                                function() {}
                            )();

                            app.$emit('success', data);
                        },
                        error(jqXHR) {
                            if (
                                jqXHR.responseJSON &&
                                jqXHR.responseJSON.message
                            )
                                alertify.error(jqXHR.responseJSON.message);
                            if (jqXHR.responseJSON && jqXHR.responseJSON.errors)
                                app.fieldErrors = jqXHR.responseJSON.errors;

                            app.sending = false;

                            (options.error || options.catch || function() {})(
                                jqXHR.responseJSON
                            );
                            (
                                options.complete ||
                                options.finally ||
                                function() {}
                            )();

                            app.$emit('error');
                        }
                    });
                }
            },
            onSubmit() {
                this.submit();
            },
            submit(postData, options) {
                this.$emit('beforeSubmit');
                postData = postData || $(this.$el).serializeArray();
                var ajax = this.call(
                    this.action,
                    Object.assign({ data: postData }, options)
                );
                this.$emit('afterSubmit');
                return ajax;
            }
        }
    };
</script>

<template>
    <form
        :action="action"
        method="post"
        autocomplete="off"
        @submit.prevent="onSubmit"
    >
        <fieldset :disabled="sending || sent">
            <slot v-bind="{ sending, sent, errors, submit, onSubmit }" />
        </fieldset>
        <fieldset v-if="button">
            <div class="form-group btn">
                <!-- {!! NoCaptcha::display(['data-theme' => 'light']) !!} -->
            </div>

            <button type="submit" class="btn btn-primary btn-lg">
                <span v-if="sent"
                    ><span class="fas fa-check-circle" />
                    {{ lang.get('form.sent') }}</span
                >
                <span v-else-if="sending"
                    ><span class="fas fa-spinner" />
                    {{ lang.get('form.sending') }}</span
                >
                <span v-else>{{ lang.get('form.send') }}</span>
            </button>
        </fieldset>
    </form>
</template>
