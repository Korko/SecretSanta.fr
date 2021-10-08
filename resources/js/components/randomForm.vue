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
                        alertify.errorAlert(this.$t('form.csv.importSuccess'));
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
                                <label for="expiration" v-tooltip.top="{ img: {'image/webp': 'srikanta-h-u-TrGVhbsUf40-unsplash.webp', 'image/jpg': 'srikanta-h-u-TrGVhbsUf40-unsplash.jpg'}, text: $t('form.data-expiration-tooltip') }">{{ $t('form.data-expiration') }}</label>
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
    @import "../../sass/layout.scss";
    @import "~vue-multiselect/dist/vue-multiselect.min";

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
        02. Header
    ========================================================================== */
    #header {
        background-image: url(../../images/gifts-1.webp);
        -moz-background-size: cover;
        background-size: cover;
        background-position: center center;
        background-attachment: fixed;
        background-repeat: no-repeat;
        position: relative;
    }
    body.nowebp #header {
        background-image: url(../../images/gifts-1.jpg);
    }

    #header .center {
        position: relative;
        z-index: 1;
        color: white;
        padding: 70px 0;
    }

    #header .bottom {
        color: white;
    }

    #header .center .slogan {
        font-size: 26px;
        text-transform: uppercase;
    }

    #header .banner h1 {
        font-size: 100px;
        color: white;
        text-transform: uppercase;
        font-weight: bold;
        display: inline-block;
        background: #a00;
        padding: 0 18px;
    }

    #header .subtitle h4 {
        display: inline-block;
        background: white;
        color: #a00;
        font-size: 38px;
        padding: 0 15px;
    }

    #header .bottom {
        text-align: center;
        width: 100%;
        position: absolute;
        bottom: 30px;
    }

    #header .bottom a {
        font-size: 36px;
        color: whitesmoke;
        position: relative;
        top: -5px;
    }

    .bg-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(44, 33, 5, 0.2);
        z-index: 0;
    }

    /* ==========================================================================
        04. Parallax
    ========================================================================== */
    .parallax {
        background: url(../../images/gifts-2.webp) fixed no-repeat center center;
        -moz-background-size: cover;
        background-size: cover;
        position: relative;
        color: #fff;
    }
    body.nowebp .parallax {
        background-image: url(../../images/gifts-2.jpg);
    }

    .parallax .inner {
        padding-top: 130px;
        padding-bottom: 130px;
    }

    .parallax.parallax2 {
        background-image: url(../../images/gifts-3.webp);
    }
    body.nowebp .parallax.parallax2 {
        background-image: url(../../images/gifts-3.jpg);
    }

    /* ==========================================================================
        05. What/How
    ========================================================================== */
    .light-wrapper {
        background: #fbfbfb;
    }

    .light-wrapper .inner {
        padding-top: 50px;
        padding-bottom: 50px;
    }

    .section-title {
        font-size: 36px;
        line-height: 40px;
        text-transform: uppercase;
        margin-bottom: 15px;
        font-weight: 600;
    }

    .main.lead {
        margin-bottom: 80px;
    }

    .lead {
        font-size: 17px;
        line-height: 24px;
        font-weight: normal;
        text-transform: uppercase;
        margin-bottom: 15px;
        color: #2e2e2e;
        position: relative;
    }

    .lead::after {
        position: absolute;
        content: ' ';
        background: #a00;
        width: 80px;
        height: 3px;
        bottom: -22px;
        left: 50%;
        margin-left: -40px;
    }

    .media {
        min-height: 128px;
    }

    .media-icon-left {
        background-position-x: left;
        padding-left: 128px;
    }
    .media-icon-right {
        background-position-x: right;
        padding-right: 128px;
    }

    .media-calendar-icon {
        background-image: url("../../images/calendar-icon.png");
        background-image: image-set(url("../../images/calendar-icon.webp"), url("../../images/calendar-icon.png"));
        background-repeat: no-repeat;
    }
    .media-user-icon {
        background-image: url("../../images/user-icon.png");
        background-image: image-set(url("../../images/user-icon.webp"), url("../../images/user-icon.png"));
        background-repeat: no-repeat;
    }
    .media-paper-icon {
        background-image: url("../../images/paper-icon.png");
        background-image: image-set(url("../../images/paper-icon.webp"), url("../../images/paper-icon.png"));
        background-repeat: no-repeat;
    }
    .media-mail-icon {
        background-image: url("../../images/mail-icon.png");
        background-image: image-set(url("../../images/mail-icon.webp"), url("../../images/mail-icon.png"));
        background-repeat: no-repeat;
    }
    .media-clock-icon {
        background-image: url("../../images/clock-icon.png");
        background-image: image-set(url("../../images/clock-icon.webp"), url("../../images/clock-icon.png"));
        background-repeat: no-repeat;
    }

    .media-body {
        width: auto;
        text-align: left;
        margin: auto;
    }

    .media-heading {
        font-weight: bold;
    }

    div.card-body {
        font-style: italic;
    }

    div.card-body::first-line {
        font-weight: bold;
        font-style: normal;
        text-transform: uppercase;
    }

    /* ==========================================================================
        06. Form
    ========================================================================== */
    fieldset > div.form-group {
        overflow: visible;
    }

    #participants caption {
        display: none;
    }

    .participant .participant-exclusions-wrapper {
        padding-top: 11px;
    }

    .participant .participant-remove-wrapper {
        width: 179px;
        white-space: nowrap;
    }

    .input-group-addon.lang {
        padding: 6px 8px;
    }

    .input-group-addon.lang::before {
        background-repeat: no-repeat;
        background-image: url(../../images/languages.webp);
        height: 11px;
        width: 14px;
        content: '';
        display: inline-block;
        margin-right: 8px;
    }
    body.nowebp .input-group-addon.lang::before {
        background-image: url(../../images/languages.png);
    }

    .input-group-addon.lang[lang=fr]::before {
        background-position: 0 -121px;
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
