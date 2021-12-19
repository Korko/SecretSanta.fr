<script>
    import jQuery from 'jquery';
    window.$ = window.jQuery = jQuery;

    import alertify from '../partials/alertify.js';
    import Toastify from '../partials/toastify.js';

    import Vue from 'vue';

    import VueAutosize from 'vue-autosize';
    Vue.use(VueAutosize);

    import Vuelidate from 'vuelidate';
    import { required, minLength, email, requiredIf } from 'vuelidate/lib/validators'
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
    import Tooltip from './tooltip.vue';
    import Toggle from './toggle.vue';

    const formatMoment = (amount, unit) => Moment(window.now).add(amount, unit).format('YYYY-MM-DD')

    export default {

        components: {
            AjaxForm,
            Csv,
            Participant,
            Tooltip,
            Toggle
        },

        data: function() {
            return {
                participantOrganizer: true,
                organizer: {
                    name: '',
                    email: '',
                },
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
            organizer: {
                name: {
                    required: requiredIf(function() {
                        return !this.participantOrganizer;
                    })
                },
                email: {
                    required: requiredIf(function() {
                        return !this.participantOrganizer;
                    }),
                    format: email
                }
            },
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
                format(value) {
                    return /^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/.test(value);
                },
                minValue(value) {
                    return Moment(value, 'YYYY-MM-DD').isSameOrAfter(formatMoment(1, 'day'));
                },
                maxValue(value) {
                    return Moment(value, 'YYYY-MM-DD').isSameOrBefore(formatMoment(6, 'month'));
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
                    name: name ? name.trim() : undefined,
                    email: email ? email.trim() : undefined,
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
                                .map(exclusion => {
                                    var idx = this.participants.findIndex(participant => (participant.name === exclusion));
                                    return idx !== -1 ? this.participants[idx] : undefined;
                                })
                                .filter(p => !!p)
                        );
                    },
                    0
                );
            },

            importParticipants(file, encoding) {
                this.importing = true;
                Papa.parse(file, {
                    encoding,
                    error: function() {
                        this.importing = false;
                        alertify.errorAlert(this.$t('form.csv.importError'));
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
                        Toastify.success(this.$t('form.csv.importSuccess'));
                    }.bind(this)
                });
            },

            appendSanta() {
                navigator.clipboard.writeText("{SANTA}");
                Toastify.success(this.$t('common.copied'));
            },

            appendTarget() {
                navigator.clipboard.writeText("{TARGET}");
                Toastify.success(this.$t('common.copied'));
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

                    <toggle
                        class="organizerToggle"
                        name="participant-organizer"
                        v-model="participantOrganizer"
                        :labelYes="$t('form.organizerIn')"
                        :labelNo="$t('form.organizerOut')"
                        backgroundYes="#0069D9"
                        backgroundNo="#B50119"
                    />

                    <fieldset v-if="!participantOrganizer" id="organizer">
                        <legend>{{ $t('form.organizer.title') }}</legend>
                        <div class="table-responsive form-group">
                            <table class="table table-hover table-numbered">
                                <caption>{{ $t('form.organizer.title') }}</caption>
                                <tbody>
                                    <tr>
                                        <th style="width: 33%" scope="col">
                                            {{ $t('form.organizer.name') }}
                                        </th>
                                        <td>
                                            <div class="input-group">
                                                <input
                                                    type="text"
                                                    name="organizer[name]"
                                                    :placeholder="$t('form.participant.name.placeholder')"
                                                    v-model="organizer.name"
                                                    class="form-control participant-name"
                                                    :class="{ 'is-invalid': $v.organizer.name.$error || fieldError(`organizer.name`)}"
                                                    :aria-invalid="$v.organizer.name.$error || fieldError(`organizer.name`)"
                                                    @blur="$v.organizer.name.$touch()"
                                                />
                                                <div v-if="!$v.organizer.name.required" class="invalid-tooltip">{{ $t('validation.custom.randomform.organizer.name.required') }}</div>
                                                <div v-else-if="fieldError(`organizer.name`)" class="invalid-tooltip">{{ fieldError(`organizer.name`) }}</div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 33%" scope="col">
                                            {{ $t('form.organizer.email') }}
                                        </th>
                                        <td>
                                            <div class="input-group">
                                                <input
                                                    type="email"
                                                    name="organizer[email]"
                                                    :placeholder="$t('form.participant.email.placeholder')"
                                                    v-model="organizer.email"
                                                    class="form-control participant-email"
                                                    :class="{ 'is-invalid': $v.organizer.email.$error || fieldError(`organizer.email`)}"
                                                    :aria-invalid="$v.organizer.email.$error || fieldError(`organizer.email`)"
                                                    @blur="$v.organizer.email.$touch()"
                                                />
                                                <div v-if="!$v.organizer.email.required" class="invalid-tooltip">{{ $t('validation.custom.randomform.organizer.email.required') }}</div>
                                                <div v-else-if="!$v.organizer.email.format" class="invalid-tooltip">{{ $t('validation.custom.randomform.organizer.email.format') }}</div>
                                                <div v-else-if="fieldError(`organizer.email`)" class="invalid-tooltip">{{ fieldError(`organizer.email`) }}</div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </fieldset>

                    <fieldset id="participants">
                        <legend>{{ $t('form.participants.title') }}</legend>
                        <div class="table-responsive form-group">
                            <table class="table table-hover table-numbered">
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
                                        :participantOrganizer="participantOrganizer"
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
                                        <p :is="$td('form.mail.content.tip1', {target: anchor('target'), santa: anchor('santa'), close:'</a>'})" @santa="appendSanta" @target="appendTarget"></p>
                                        <p>{{ $t('form.mail.content.tip2') }}</p>
                                    </blockquote>
                                </div>
                            </fieldset>
                        </div>
                    </fieldset>
                    <fieldset>
                        <div id="form-options" class="form-group">
                            <div class="input-inline-group">
                                <tooltip direction="top">
                                    <template #tooltip>
                                        <picture>
                                            <source srcset="../../images/srikanta-h-u-TrGVhbsUf40-unsplash.webp" type="image/webp" />
                                            <source srcset="../../images/srikanta-h-u-TrGVhbsUf40-unsplash.jpg" type="image/jpg" />
                                            <img class="media-object" src="../../images/srikanta-h-u-TrGVhbsUf40-unsplash.jpg" />
                                        </picture>
                                        <div class="text-content">
                                            <h3>{{ $t('form.data-expiration-tooltip.title') }}</h3>
                                            <ul>
                                                <li>{{ $t('form.data-expiration-tooltip.interface') }}</li>
                                                <li>{{ $t('form.data-expiration-tooltip.deletion') }}</li>
                                            </ul>
                                        </div>
                                    </template>
                                    <template #default>
                                        <label for="expiration">{{ $t('form.data-expiration') }}</label>
                                    </template>
                                </tooltip>
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
                                <div class="invalid-tooltip" v-else-if="!$v.expiration.format">{{ $t('validation.custom.randomform.expiration.format') }}</div>
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

<style scoped>
    @import "~vue-multiselect/dist/vue-multiselect.min";
    @import "~toastify-js/src/toastify.css";

    .organizerToggle {
        display: inline-block;
    }
    #participants {
        margin-top: 80px;
    }

    /* Fix tiny style glitches */
    .multiselect--active .multiselect__tags {
        min-height: 41px;
    }

    .multiselect__input, .multiselect__single {
        padding: 0 !important;
        line-height: 1.5 !important;
    }

    .multiselect, .multiselect__input, .multiselect__single {
        font-size: 14px !important;
    }

    .multiselect__placeholder {
        padding-top: 0 !important;
    }

    /* ==========================================================================
        06. Form
    ========================================================================== */
    fieldset > div.form-group {
        overflow: visible;
    }

    table caption {
        display: none;
    }

    .participant .participant-exclusions-wrapper {
        padding-top: 11px;
    }

    .participant .participant-remove-wrapper {
        width: 179px;
        white-space: nowrap;
    }

    #mailContent,
    #mailPost {
        max-width: 100%;
        resize: none;
    }

    #mailContent {
        border-bottom: 0;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
    }

    #mailPost {
        border-top: 0;
        border-top-right-radius: 0;
        border-top-left-radius: 0;
        height: 55px;
    }

    #mailPost.extended {
        height: 75px;
    }

    #form {
        padding-bottom: 20px;
    }

    #form .container {
        transition: all 0.4s;
        position: relative;
    }

    #form.success .success-wrapper {
        display: block;
    }

    .col-xs-0 {
        width: 1%;
    }

    .border-left {
        border-left: 1px solid #ddd;
    }

    .border-right {
        border-right: 1px solid #ddd;
    }

    .row.text-only {
        line-height: 34px;
    }

    .input-inline-group {
        position: relative;
        display: inline-block;
    }

    .table-bordered.heavy-borders,
    .table-bordered.heavy-borders > tbody > tr > td,
    .table-bordered.heavy-borders > tbody > tr > th,
    .table-bordered.heavy-borders > tfoot > tr > td,
    .table-bordered.heavy-borders > tfoot > tr > th,
    .table-bordered.heavy-borders > thead > tr > td,
    .table-bordered.heavy-borders > thead > tr > th {
        border-width: 3px;
    }

    .tips {
        font-size: 12px;
        font-style: italic;
        box-shadow: 3px 3px 6px 0 #bbb;
        margin: 10px auto 0 auto;
        display: inline-block;
        border: 1px solid #c0c0c0;
        background-color: #ddd;
        padding: 10px;
    }

    .tips p {
        margin: 0;
    }

    .fade-enter-active, .fade-leave-active, .fade-move {
        transition: all 1s;
    }

    .fade-enter, .fade-leave-to {
        opacity: 0;
    }

    /* ==========================================================================
        08. Responsive styles
    ========================================================================== */
    @media (max-width: 1199px) {
        .participant-remove span {
            display: none;
        }
    }

    @media (max-width: 767px) {
        #header .banner h1 {
            font-size: 60px;
        }

        #header .subtitle h4 {
            font-size: 22px;
        }
    }

    /* ==========================================================================
        12. Widgets
    ========================================================================== */
    .participants-import button .fa-spinner {
        margin-right: 10px;
    }
</style>
