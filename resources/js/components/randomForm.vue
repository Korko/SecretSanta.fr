<script>
    import jQuery from 'jquery';

    import alertify from 'alertify.js';

    import Vue from 'vue';

    import VueAutosize from 'vue-autosize';
    Vue.use(VueAutosize);

    import Vuelidate from 'vuelidate';
    import { required, minLength } from 'vuelidate/lib/validators'
    Vue.use(Vuelidate);

    import Modernizr from '../partials/modernizr.js';
    import Moment from 'moment';
    import Papa from 'papaparse';

    import Lang from '../partials/lang.js';

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
                title: '',
                content: '',
                expiration: Moment(window.now)
                    .add(1, 'day')
                    .format('YYYY-MM-DD'),
                now: window.now,
                showModal: false,
                importing: false
            };
        },

        computed: {
            participantNames() {
                var names = {};
                this.participants.forEach((participant, idx) => {
                    if (participant.name) {
                        names[idx] = participant.name;
                    }
                });
                return names;
            }
        },

        validations: {
            participants: {
                required,
                minLength: minLength(3)
            },
            title: {
                required
            },
            content: {
                required,
                containsTarget(value) {
                    return value.indexOf('{TARGET}') >= 0;
                }
            },
            expiration() {
                return {
                    required,
                    minValue: this.moment(1, 'day'),
                    maxValue: this.moment(1, 'year')
                };
            }
        },

        watch: {
            sending(newVal) {
                // If we reset the sending status, reset the captcha
                if (!newVal) {
                    grecaptcha && grecaptcha.reset(); // eslint-disable-line no-undef
                }
            },

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
                            minDate: this.moment(1, 'day'),
                            maxDate: this.moment(1, 'year')
                        });
                    }

                    if (!Modernizr.filereader) {
                        jQuery('.participants-imports').remove();
                    }
                }.bind(this)
            );
        },

        methods: {
            t(key, params) {
                return Lang.get(key, params);
            },

            // Only way to have parameters parsing for vuejs events
            td(key, params) {
                var data = this.t(key, params);
                return {
                    name: "dynamic-string",
                    template: `<p>${data}</p>`
                }
            },

            // Just because I couldn't handle too much depth with quotes
            anchor(event) {
                return `<a href="" @click.prevent=\'\$emit("${event}")\'>`;
            }, 

            moment(amount, unit) {
                return Moment(this.now)
                    .add(amount, unit)
                    .format('YYYY-MM-DD');
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
                    () => (
                        this.participants[n - 1].exclusions = (exclusions || '')
                            .split(',')
                            .map(s => s.trim())
                            .filter(s => !!s)
                            .map(exclusion => {
                                var participant = this.participants.find(participant => (participant.name === exclusion));
                                if(participant) return ['id', 'text'].map(key => participant[key]);
                            })
                            .filter(s => !!s)
                    ),
                    0
                );
            },

            importParticipants(file) {
                this.importing = true;
                Papa.parse(file, {
                    error: function() {
                        this.importing = false;
                        alertify.alert(this.t('csv.importError'));
                    },
                    complete: function(file) {
                        this.importing = false;
                        this.resetParticipants();

                        // Set participants
                        file.data.forEach(
                            function(participant) {
                                if (participant[0] !== '') {
                                    this.addParticipant(participant[0], participant[1], participant[2]);
                                }
                            }.bind(this)
                        );

                        if (this.participants.length < 3) {
                            for (var i = 0; i < 3 - this.participants.length; i++) {
                                this.addParticipant();
                            }
                        }
                        alertify.alert(this.t('csv.importSuccess'));
                    }.bind(this)
                });
            },

            appendSanta() {
                this.content += "{SANTA}";
            },

            appendTarget() {
                this.content += "{TARGET}";
            }
        }
    };
</script>

<template>
    <div>
        <div v-cloak class="row text-center form">
            <ajax-form id="randomForm" action="/" :button_send="t('form.submit')">
                <template #default="{ sending, sent, errors }">
                    <div v-show="sent" id="success-wrapper" class="alert alert-success">
                        {{ t('form.success') }}
                    </div>

                    <div v-show="errors.length && !sent" id="errors-wrapper" class="alert alert-danger">
                        <ul id="errors">
                            <li v-for="(error, idx) in errors" :key="idx">@{{ error }}</li>
                        </ul>
                    </div>

                    <fieldset>
                        <legend>{{ t('form.participants') }}</legend>
                        <div class="table-responsive form-group">
                            <table id="participants" class="table table-hover table-numbered">
                                <thead>
                                    <tr>
                                        <th style="width: 33%" scope="col">
                                            {{ t('form.participant.name') }}
                                        </th>
                                        <th style="width: 33%" scope="col">
                                            {{ t('form.participant.email') }}
                                        </th>
                                        <th style="width: 30%" scope="col">
                                            {{ t('form.participant.exclusions') }}
                                        </th>
                                        <th style="width: 3%" scope="col" />
                                    </tr>
                                </thead>
                                <tbody is="transition-group" type="transition" name="fade">
                                    <!-- Default is three empty rows to have three entries at any time -->
                                    <tr
                                        is="participant"
                                        v-for="(participant, idx) in participants"
                                        :key="participant.id"
                                        v-bind="participant"
                                        :names="participantNames"
                                        :idx="idx"
                                        @input:name="$set(participant, 'name', $event)"
                                        @input:email="$set(participant, 'email', $event)"
                                        @input:exclusions="$set(participant, 'exclusions', $event)"
                                        @delete="participants.splice(idx, 1)"
                                    />
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-success participant-add" @click="addParticipant()">
                                <i class="fas fa-plus" />
                                {{ t('form.participant.add') }}
                            </button>
                            <button
                                type="button"
                                class="btn btn-warning participants-import"
                                :disabled="importing"
                                @click="showModal = true"
                            >
                                <span v-if="importing"
                                    ><i class="fas fa-spinner fa-spin" />
                                    {{ t('form.participants.importing') }}</span
                                >
                                <span v-else
                                    ><i class="fas fa-list-alt" /> {{ t('form.participants.import') }}</span
                                >
                            </button>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Messages</legend>
                        <div id="contact">
                            <fieldset id="form-mail-group">
                                <div class="form-group">
                                    <label for="mailTitle">{{ t('form.mail.title') }}</label>
                                    <input
                                        id="mailTitle"
                                        type="text"
                                        name="title"
                                        :placeholder="t('form.mail.title.placeholder')"
                                        v-model="title"
                                        class="form-control"
                                        :class="{ 'is-invalid': $v.title.$error }"
                                        :aria-invalid="$v.title.$error"
                                    />
                                </div>
                                <div class="form-group">
                                    <label for="mailContent">{{ t('form.mail.content') }}</label>
                                    <textarea
                                        id="mailContent"
                                        v-autosize
                                        name="content-email"
                                        :placeholder="t('form.mail.content.placeholder')"
                                        class="form-control"
                                        :class="{ 'is-invalid': $v.content.$error }"
                                        :aria-invalid="$v.content.$error"
                                        rows="3"
                                        v-model="content"
                                    />
                                    <textarea
                                        id="mailPost"
                                        class="form-control extended"
                                        read-only
                                        disabled
                                        :value="t('form.mail.post2')"
                                    />

                                    <blockquote class="tips">
                                        <p :is="td('form.mail.content.tip1', {'open-target': anchor('target'), 'open-santa': anchor('santa'), 'close':'</a>'})" @santa="appendSanta" @target="appendTarget"></p>
                                        <p>{{ t('form.mail.content.tip2') }}</p>
                                    </blockquote>
                                </div>
                            </fieldset>
                        </div>
                    </fieldset>
                    <fieldset>
                        <div id="form-options" class="form-group">
                            <label
                                >{{ t('form.data-expiration')
                                }}<input
                                    type="date"
                                    name="data-expiration"
                                    v-model="expiration"
                                    :min="moment(1, 'day')"
                                    :max="moment(1, 'year')"
                            /></label>
                        </div>
                    </fieldset>
                </template>
            </ajax-form>
        </div>
        <div id="errors-wrapper" class="alert alert-danger v-rcloak">
            {{ t('form.waiting') }}
        </div>
        <csv v-if="showModal" @import="importParticipants" @close="showModal = false" />
    </div>
</template>

<style>
  .fade-enter-active, .fade-leave-active, .fade-move {
    transition: all 1s
  }

  .fade-enter, .fade-leave-to {
    opacity: 0
  }
</style>
