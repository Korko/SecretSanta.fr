<script>
    import jQuery from 'jquery';
    window.$ = window.jQuery = jQuery;
    import 'jquery-ui/ui/widgets/datepicker.js';

    import alertify from '../partials/alertify.js';

    import Vue from 'vue';

    import VueAutosize from 'vue-autosize';
    Vue.use(VueAutosize);

    import Vuelidate from 'vuelidate';
    import { required, minLength, email } from 'vuelidate/lib/validators'
    Vue.use(Vuelidate);

    // Still using CommonJS syntax
    const Modernizr = require("Modernizr");

    import Moment from 'moment';
    import 'moment/locale/fr';
    Moment.locale('fr');

    import Papa from 'papaparse';

    import Csv from './csv.vue';
    import AjaxForm from './ajaxForm.vue';
    import Participant from './participant.vue';

    const formatMoment = (amount, unit) => Moment(window.now).add(amount, unit).format('YYYY-MM-DD')

    export default {
        components: {
            AjaxForm,
            Csv,
            Participant
        },

        data: function() {
            return {
                participants: [],
                title: '',
                content: '',
                expiration: null,
                now: window.now,
                showModal: false,
                importing: false,
            };
        },

        validations: {
            participants: {
                required,
                minLength: minLength(3),
                $each: {
                    $trackBy: 'id',
                    name: {
                        required,
                        unique(value) {
                            // standalone validator ideally should not assume a field is required
                            if (value === '') return true;

                            return (this.participants.filter(participant => (participant.name === value)).length === 1);
                        }
                    },
                    email: {
                        required,
                        format: email
                    }
                }
            },
            title: {
                required
            },
            content: {
                required,
                contains(value) {
                   return value.indexOf('{TARGET}') >= 0;
                }
            },
            expiration: {
                required,
                minValue(value) {
                    return Moment(value, 'YYYY-MM-DD').isSameOrAfter(formatMoment(1, 'day'));
                },
                maxValue(value) {
                    return Moment(value, 'YYYY-MM-DD').isSameOrBefore(formatMoment(1, 'year'));
                }
            }
        },

        watch: {
            sent(newVal) {
                // If sent is a success, scroll to the message
                if (newVal) {
                    jQuery.scrollTo('#form .row', 800, { offset: -120 });
                }
            },

            errors(newVal) {
                // If there's new errors, scroll to them
                if (newVal.length) {
                    jQuery.scrollTo('#form .row', 800, { offset: -120 });
                }
            }
        },

        created: function() {
            this.addParticipant();
            this.addParticipant();
            this.addParticipant();

            Vue.nextTick(
                function() {
                    if (!Modernizr.inputtypes.date) {
                        jQuery('input[type=date]', this.$el).datepicker({
                            // Consistent format with the HTML5 picker
                            dateFormat: 'yy-mm-dd',
                            minDate: formatMoment(1, 'day'),
                            maxDate: formatMoment(1, 'year')
                        });
                    }

                    if (!Modernizr.filereader) {
                        jQuery('.participants-imports').remove();
                    }
                }.bind(this)
            );
        },

        methods: {
            // Only way to have parameters parsing for vuejs events
            $td(key, params) {
                var data = this.$t(key, params);
                return {
                    name: "dynamic-string",
                    template: `<p>${data}</p>`
                }
            },

            // Just because I couldn't handle too much depth with quotes
            anchor(event) {
                return `<a class="link" @click.prevent='\$emit("${event}")'>`;
            },

            moment(amount, unit) {
                return formatMoment(amount, unit);
            },

            resetParticipants() {
                this.participants = [];
            },

            addParticipant(name, email, exclusions) {
                var n = this.participants.push({
                    name: name,
                    email: email,
                    id: 'id' + this.participants.length + new Date().getTime()
                });
                setTimeout(
                    () => {
                        this.$set(
                            this.participants[n - 1],
                            'exclusions',
                            (exclusions || '')
                                .split(',')
                                .map(s => s.trim())
                                .filter(exclusion => {
                                    return (
                                        this.participants.findIndex(participant => (participant.name === exclusion)) !== -1
                                    );
                                })
                        );
                    },
                    0
                );
            },

            importParticipants(file) {
                this.importing = true;
                Papa.parse(file, {
                    error: function() {
                        this.importing = false;
                        alertify.alert(this.$t('form.csv.importError'));
                    },
                    complete: function(file) {
                        this.importing = false;
                        this.resetParticipants();

                        // Set participants
                        file.data.forEach(
                            function(participant) {
                                if (participant[0] !== '' && participant.length >= 2) {
                                    this.addParticipant(participant[0], participant[1], participant[2]);
                                }
                            }.bind(this)
                        );

                        if (this.participants.length < 3) {
                            for (var i = 0; i < 3 - this.participants.length; i++) {
                                this.addParticipant();
                            }
                        }
                        alertify.alert(this.$t('form.csv.importSuccess'));
                    }.bind(this)
                });
            },

            appendSanta() {
                this.content += "{SANTA}";
                this.$v.content.$touch();
            },

            appendTarget() {
                this.content += "{TARGET}";
                this.$v.content.$touch();
            },

            reset() {
                this.participants = [];
                this.title = '';
                this.content = '';
                this.expiration = null;

                this.addParticipant();
                this.addParticipant();
                this.addParticipant();
            }
        }
    };
</script>

<template>
    <div>
        <div v-cloak class="row text-center form">
            <ajax-form id="randomForm" action="/" :button-send="$t('form.submit')" :$v="$v" @reset="reset" send-icon="dice">
                <template #default="{ sending, sent, fieldError }">
                    <div v-show="sent" id="success-wrapper" class="alert alert-success">
                        {{ $t('form.success') }}
                    </div>

                    <fieldset>
                        <legend>{{ $t('form.participants.title') }}</legend>
                        <div class="table-responsive form-group">
                            <div class="alert alert-warning" role="alert">
                                Les adresses @laposte.net et @sfr.fr ne fonctionnent malheureusement pas bien avec SecretSanta.fr en ce moment. Les destinataires ne reçoivent pas leurs emails.<br />Autant que possible, évitez d'utilisez ces adresses.
                            </div>
                            <table id="participants" class="table table-hover table-numbered">
                                <caption>{{ $t('form.participants.caption') }}</caption>
                                <thead>
                                    <tr>
                                        <th style="width: 33%" scope="col">
                                            {{ $t('form.participant.name.label') }}
                                        </th>
                                        <th style="width: 33%" scope="col">
                                            {{ $t('form.participant.email.label') }}
                                        </th>
                                        <th style="width: 30%" scope="col">
                                            {{ $t('form.participant.exclusions.label') }}
                                        </th>
                                        <th style="width: 3%" scope="col" />
                                    </tr>
                                </thead>
                                <tbody is="transition-group" type="transition" name="fade">
                                    <!-- Default is three empty rows to have three entries at any time -->
                                    <tr
                                        is="participant"
                                        v-for="(participant, idx) in participants"
                                        :key="idx"
                                        :id="participant.id"
                                        :idx="idx"
                                        :name="participant.name"
                                        :email="participant.email"
                                        :exclusions="participant.exclusions"
                                        :all="participants"
                                        :required="idx < 3 && participants.length <= 3"
                                        :field-error="fieldError"
                                        :$v="$v.participants.$each[idx]"
                                        @input:name="$set(participant, 'name', $event)"
                                        @input:email="$set(participant, 'email', $event)"
                                        @input:exclusions="$set(participant, 'exclusions', $event)"
                                        @removeExclusion="participant.exclusions.remove($event)"
                                        @addExclusion="participant.exclusions.push($event)"
                                        @delete="participants.splice(idx, 1)"
                                    />
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-outline-success participant-add" @click="addParticipant()">
                                <i class="fas fa-plus" />
                                {{ $t('form.participant.add') }}
                            </button>
                            <button
                                type="button"
                                class="btn btn-outline-warning participants-import"
                                :disabled="importing"
                                @click="showModal = true"
                            >
                                <span v-if="importing"
                                    ><i class="fas fa-spinner fa-spin" />
                                    {{ $t('form.participants.importing') }}</span
                                >
                                <span v-else
                                    ><i class="fas fa-list-alt" /> {{ $t('form.participants.import') }}</span
                                >
                            </button>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Messages</legend>
                        <div id="contact">
                            <fieldset id="form-mail-group">
                                <div class="form-group">
                                    <label for="mailTitle">{{ $t('form.mail.title.label') }}</label>
                                    <div class="input-group">
                                        <input
                                            id="mailTitle"
                                            type="text"
                                            name="title"
                                            v-model="title"
                                            :placeholder="$t('form.mail.title.placeholder')"
                                            class="form-control"
                                            :class="{ 'is-invalid': $v.title.$error || fieldError('title') }"
                                            :aria-invalid="$v.title.$error || fieldError('title')"
                                        />
                                        <div class="invalid-tooltip">{{ $t('validation.custom.randomform.title.required') }}</div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="mailContent">{{ $t('form.mail.content.label') }}</label>
                                    <div class="input-group">
                                        <textarea
                                            id="mailContent"
                                            v-autosize
                                            name="content-email"
                                            v-model="content"
                                            :placeholder="$t('form.mail.content.placeholder')"
                                            rows="3"
                                            class="form-control"
                                            :class="{ 'is-invalid': $v.content.$error || fieldError('content-email') }"
                                            :aria-invalid="$v.content.$error || fieldError('content-email')"
                                        />
                                        <div v-if="!$v.content.required" class="invalid-tooltip">{{ $t('validation.custom.randomform.content.required') }}</div>
                                        <div v-else-if="!$v.content.contains" class="invalid-tooltip">{{ $t('validation.custom.randomform.content.contains') }}</div>
                                        <div v-else-if="fieldError('content-email')" class="invalid-tooltip">{{ fieldError('content-email') }}</div>
                                    </div>
                                    <textarea
                                        id="mailPost"
                                        class="form-control extended"
                                        read-only
                                        disabled
                                        :value="$t('form.mail.post')"
                                    />

                                    <blockquote class="tips">
                                        <p :is="$td('form.mail.content.tip1', {'target': anchor('target'), 'santa': anchor('santa'), 'close':'</a>'})" @santa="appendSanta" @target="appendTarget"></p>
                                        <p>{{ $t('form.mail.content.tip2') }}</p>
                                    </blockquote>
                                </div>
                            </fieldset>
                        </div>
                    </fieldset>
                    <fieldset>
                        <div id="form-options" class="form-group">
                            <div class="input-inline-group">
                                <label for="expiration" v-tooltip.top="{ img: 'srikanta-h-u-TrGVhbsUf40-unsplash.jpg', text: $t('form.data-expiration-tooltip') }">{{ $t('form.data-expiration') }}</label>
                                <input
                                    type="date"
                                    name="data-expiration"
                                    id="expiration"
                                    v-model="expiration"
                                    :class="{ 'is-invalid': $v.expiration.$error || fieldError('data-expiration') }"
                                    :aria-invalid="$v.expiration.$error || fieldError('data-expiration')"
                                    @blur="$v.expiration.$touch()"
                                    :min="moment(1, 'day')"
                                    :max="moment(1, 'year')"
                                />
                                <div class="invalid-tooltip" v-if="!$v.expiration.required">{{ $t('validation.custom.randomform.expiration.required') }}</div>
                                <div class="invalid-tooltip" v-else-if="!$v.expiration.minValue">{{ $t('validation.custom.randomform.expiration.min') }}</div>
                                <div class="invalid-tooltip" v-else-if="!$v.expiration.maxValue">{{ $t('validation.custom.randomform.expiration.max') }}</div>
                                <div class="invalid-tooltip" v-else-if="fieldError('data-expiration')">{{ fieldError('data-expiration') }}</div>
                            </div>
                        </div>
                    </fieldset>
                </template>
            </ajax-form>
        </div>
        <div id="errors-wrapper" class="alert alert-danger v-rcloak">
            {{ $t('form.waiting') }}
        </div>
        <csv v-if="showModal" @import="importParticipants" @close="showModal = false" />
    </div>
</template>

<style>
    .fade-enter-active, .fade-leave-active, .fade-move {
        transition: all 1s;
    }

    .fade-enter, .fade-leave-to {
        opacity: 0;
    }
</style>
