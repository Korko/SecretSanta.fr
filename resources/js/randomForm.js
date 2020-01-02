import jQuery from 'jquery';

import alertify from 'alertify.js';

import Vue from 'vue';
import VueAutosize from 'vue-autosize';
Vue.use(VueAutosize);

import Modernizr from './modernizr.js';
import Moment from 'moment';
import Papa from 'papaparse';

import Lang from './partials/lang.js';
import store from './partials/store.js';

import Multiselect from 'vue-multiselect';

import Csv from './components/csv.vue';
import AjaxForm from './components/ajaxForm.vue';

window.app = new Vue({
    el: '#form',

    store,

    data: {
        participants: [],
        date: window.now,
        showModal: false,
        importing: false
    },

    components: {
        AjaxForm,
        Csv,
        participant: {
            template: '#participant-template',
            props: {
                idx: {
                    type: Number,
                    required: true
                },
                name: {
                    type: String,
                    default: ''
                },
                email: {
                    type: String,
                    default: ''
                },
                participants: {
                    type: Array,
                    required: true
                }
            },
            data: function() {
                return {
                    exclusions: []
                };
            },
            components: {
                Multiselect
            },
            computed: {
                participantNames: function() {
                    var names = [];
                    this.participants.forEach(
                        function(participant, idx) {
                            if (participant.name && idx !== this.idx) {
                                names.push({
                                    id: participant.id,
                                    value: idx,
                                    text: participant.name
                                });
                            }
                        }.bind(this)
                    );
                    return names;
                }
            },
            watch: {
                name: function() {
                    this.$emit('changename', this.name);
                },
                email: function() {
                    this.$emit('changeemail', this.email);
                }
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
                    $('input[type=date]', this.$el).datepicker({
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
                    $('.participants-imports').remove();
                }
            }.bind(this)
        );
    },

    filters: {
        moment: function(date, amount, unit) {
            return Moment(date)
                .add(amount, unit)
                .format('YYYY-MM-DD');
        }
    },

    watch: {
        sending: function(newVal) {
            // If we reset the sending status, reset the captcha
            if (!newVal) {
                grecaptcha.reset();
            }
        },

        sent: function(newVal) {
            // If sent is a success, scroll to the message
            if (newVal) {
                $.scrollTo('#form .row', 800, { offset: -120 });
            }
        },

        errors: function(newVal) {
            // If there's new errors, scroll to them
            if (newVal.length) {
                $.scrollTo('#form .row', 800, { offset: -120 });
            }
        }
    },

    methods: {
        resetParticipants: function() {
            this.participants = [];
        },

        addParticipant: function(name, email) {
            this.participants.push({
                name: name,
                email: email,
                id: 'id' + this.participants.length + new Date().getTime()
            });
        },

        importParticipants: function(file) {
            this.importing = true;
            var test = Papa.parse(file, {
                error: function() {
                    this.importing = false;
                    alertify.alert(Lang.get('csv.importError'));
                },
                complete: function(file) {
                    this.importing = false;
                    this.resetParticipants();
                    file.data.forEach(
                        function(participant) {
                            if (participant[0] !== '') {
                                this.addParticipant(
                                    participant[0],
                                    participant[1]
                                );
                            }
                        }.bind(this)
                    );
                    if (this.participants.length < 3) {
                        for (var i = 0; i < 3 - this.participants.length; i++) {
                            this.addParticipant();
                        }
                    }
                    alertify.alert(Lang.get('csv.importSuccess'));
                }.bind(this)
            });
        }
    }
});
