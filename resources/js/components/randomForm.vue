<script>
    import jQuery from 'jquery';

    import alertify from 'alertify.js';

    import Vue from 'vue';
    import VueAutosize from 'vue-autosize';
    Vue.use(VueAutosize);

    import Modernizr from '../partials/modernizr.js';
    import Moment from 'moment';
    import Papa from 'papaparse';

    import { mapState } from 'vuex';

    import Csv from './csv.vue';
    import AjaxForm from './ajaxForm.vue';
    import Participant from './participant.vue';

    export default {
        components: {
            AjaxForm,
            Csv,
            Participant
        },

        data: function() {
            return {
                participants: [],
                date: window.now,
                showModal: false,
                importing: false
            };
        },

        computed: mapState(['lang']),

        watch: {
            sending: function(newVal) {
                // If we reset the sending status, reset the captcha
                if (!newVal) {
                    grecaptcha && grecaptcha.reset(); // eslint-disable-line no-undef
                }
            },

            sent: function(newVal) {
                // If sent is a success, scroll to the message
                if (newVal) {
                    jQuery.scrollTo('#form .row', 800, { offset: -120 });
                }
            },

            errors: function(newVal) {
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
                            minDate: Moment(this.now)
                                .add(1, 'day')
                                .toDate(),
                            maxDate: Moment(this.now)
                                .add(1, 'year')
                                .toDate()
                        });
                    }

                    if (!Modernizr.filereader) {
                        jQuery('.participants-imports').remove();
                    }
                }.bind(this)
            );
        },

        methods: {
            moment: function(date, amount, unit) {
                return Moment(date)
                    .add(amount, unit)
                    .format('YYYY-MM-DD');
            },

            resetParticipants: function() {
                this.participants = [];
            },

            addParticipant: function(name, email, exclusions) {
                var n = this.participants.push({
                    name: name,
                    email: email,
                    id: 'id' + this.participants.length + new Date().getTime()
                });
                setTimeout(
                    () =>
                        (this.participants[n - 1].exclusions = (
                            exclusions || ''
                        )
                            .split(',')
                            .map(s => s.trim())
                            .filter(s => !!s)
                            .map(exclusion => {
                                var participant = this.participants.find(
                                    participant =>
                                        (participant.name = exclusion)
                                );
                                return {
                                    id: participant.id,
                                    text: participant.name
                                };
                            })),
                    0
                );
            },

            importParticipants: function(file) {
                this.importing = true;
                Papa.parse(file, {
                    error: function() {
                        this.importing = false;
                        alertify.alert(this.lang.get('csv.importError'));
                    },
                    complete: function(file) {
                        this.importing = false;
                        this.resetParticipants();

                        // Set participants
                        file.data.forEach(
                            function(participant) {
                                if (participant[0] !== '') {
                                    this.addParticipant(
                                        participant[0],
                                        participant[1],
                                        participant[2]
                                    );
                                }
                            }.bind(this)
                        );

                        if (this.participants.length < 3) {
                            for (
                                var i = 0;
                                i < 3 - this.participants.length;
                                i++
                            ) {
                                this.addParticipant();
                            }
                        }
                        alertify.alert(this.lang.get('csv.importSuccess'));
                    }.bind(this)
                });
            }
        }
    };
</script>

<template>
    <div>
        <div v-cloak class="row text-center form">
            <ajax-form id="randomForm" action="/">
                <template #default="{ sending, sent, errors }">
                    <div
                        v-show="sent"
                        id="success-wrapper"
                        class="alert alert-success"
                    >
                        {{ lang.get('form.success') }}
                    </div>

                    <div
                        v-show="errors.length && !sent"
                        id="errors-wrapper"
                        class="alert alert-danger"
                    >
                        <ul id="errors">
                            <li v-for="(error, idx) in errors" :key="idx">
                                @{{ error }}
                            </li>
                        </ul>
                    </div>

                    <fieldset>
                        <legend>{{ lang.get('form.participants') }}</legend>
                        <div class="table-responsive form-group">
                            <table
                                id="participants"
                                class="table table-hover table-numbered"
                            >
                                <thead>
                                    <tr>
                                        <th style="width: 33%" scope="col">
                                            {{
                                                lang.get(
                                                    'form.participant.name'
                                                )
                                            }}
                                        </th>
                                        <th style="width: 33%" scope="col">
                                            {{
                                                lang.get(
                                                    'form.participant.email'
                                                )
                                            }}
                                        </th>
                                        <th style="width: 30%" scope="col">
                                            {{
                                                lang.get(
                                                    'form.participant.exclusions'
                                                )
                                            }}
                                        </th>
                                        <th style="width: 3%" scope="col" />
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Default is three empty rows to have three entries at any time -->
                                    <tr
                                        is="participant"
                                        v-for="(participant,
                                        idx) in participants"
                                        :key="participant.id"
                                        :name="participant.name"
                                        :email="participant.email"
                                        :exclusions="participant.exclusions"
                                        :participants="participants"
                                        :idx="idx"
                                        @input:name="participant.name = $event"
                                        @input:email="
                                            participant.email = $event
                                        "
                                        @input:exclusions="
                                            participant.exclusions = $event
                                        "
                                        @delete="participants.splice(idx, 1)"
                                    />
                                </tbody>
                            </table>
                            <button
                                type="button"
                                class="btn btn-success participant-add"
                                @click="addParticipant()"
                            >
                                <i class="fas fa-plus" />
                                {{ lang.get('form.participant.add') }}
                            </button>
                            <button
                                type="button"
                                class="btn btn-warning participants-import"
                                :disabled="importing"
                                @click="showModal = true"
                            >
                                <span v-if="importing"
                                    ><i class="fas fa-spinner fa-spin" />
                                    {{
                                        lang.get('form.participants.importing')
                                    }}</span
                                >
                                <span v-else
                                    ><i class="fas fa-list-alt" />
                                    {{
                                        lang.get('form.participants.import')
                                    }}</span
                                >
                            </button>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Messages</legend>
                        <div id="contact">
                            <fieldset id="form-mail-group">
                                <div class="form-group">
                                    <label for="mailTitle">{{
                                        lang('form.mail.title')
                                    }}</label>
                                    <input
                                        id="mailTitle"
                                        type="text"
                                        name="title"
                                        :required="emailUsed"
                                        :placeholder="
                                            lang.get(
                                                'form.mail.title.placeholder'
                                            )
                                        "
                                        value=""
                                        class="form-control"
                                    />
                                </div>
                                <div class="form-group">
                                    <label for="mailContent">{{
                                        lang.get('form.mail.content')
                                    }}</label>
                                    <textarea
                                        id="mailContent"
                                        v-autosize
                                        name="content-email"
                                        :required="emailUsed"
                                        :placeholder="
                                            lang.get(
                                                'form.mail.content.placeholder'
                                            )
                                        "
                                        class="form-control"
                                        rows="3"
                                    />
                                    <textarea
                                        id="mailPost"
                                        class="form-control extended"
                                        read-only
                                        disabled
                                        :value="lang.get('form.mail.post2')"
                                    />

                                    <blockquote class="tips">
                                        <p>@lang('form.mail.content.tip1')</p>
                                        <p>@lang('form.mail.content.tip2')</p>
                                    </blockquote>
                                </div>
                            </fieldset>
                        </div>
                    </fieldset>
                    <fieldset>
                        <div id="form-options" class="form-group">
                            <label
                                >{{ lang.get('form.data-expiration')
                                }}<input
                                    type="date"
                                    name="data-expiration"
                                    :min="moment(date, 1, 'day')"
                                    :max="moment(date, 1, 'year')"
                            /></label>
                        </div>
                    </fieldset>
                    <fieldset>
                        <div class="form-group btn">
                            <!-- {!! NoCaptcha::display(['data-theme' => 'light']) !!} -->
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg">
                            <span v-if="sending"
                                ><i class="fas fa-spinner fa-spin" />
                                {{ lang.get('form.sending') }}</span
                            >
                            <span v-else-if="sent"
                                ><i class="fas fa-check-circle" />
                                {{ lang.get('form.sent') }}</span
                            >
                            <span v-else>{{ lang.get('form.submit') }}</span>
                        </button>
                    </fieldset>
                </template>
            </ajax-form>
        </div>
        <div id="errors-wrapper" class="alert alert-danger v-rcloak">
            {{ lang.get('form.waiting') }}
        </div>
        <csv
            v-if="showModal"
            @import="importParticipants"
            @close="showModal = false"
        />
    </div>
</template>
