import Toastify from '@/Modules/toastify.js';
import scrollTo from '@/Modules/scrollTo.js';
import Echo from '@/Modules/echo.js';

//import Moment from 'moment';
//import 'moment/locale/fr';
//Moment.locale('fr');

import Papa from 'papaparse';

import { createApp } from 'vue/dist/vue.esm-bundler.js';

import { createI18n } from 'vue-i18n';

import { useForm } from 'laravel-precognition-vue';

import Csv from '@/Components/CSV.vue';
import Multiselect from '@vueform/multiselect';
import MyInput from '@/Components/MyInput.vue';
import Tooltip from '@/Components/Tooltip.vue';
import Toggle from '@/Components/Toggle.vue';

const i18n = createI18n({
    locale: window.global.locale,
    messages: window.global.translations,
    globalInjection: true
});

window.app = createApp({
    components: {
        Csv,
        Multiselect,
        MyInput,
        Tooltip,
        Toggle
    },

    data: () => ({
        form: useForm('post', route('form.process'), {
            'participant-organizer': true,
            organizer: {
                name: '',
                email: '',
            },
            participants: [],
            title: '',
            content: '',
        }),
        showModal: false,
        importing: false,
        sending: false,
        sent: false,
        draw: null,
        draw_status: 'pending'
    }),

    watch: {
        sent(newVal) {
            // If sent is a success, scroll to the message
            if (newVal) {
                scrollTo('#form .row', 800, { offset: -120 });
            }
        },

        errors(newVal) {
            // If there's new errors, scroll to them
            if (newVal.length) {
                scrollTo('#form .row', 800, { offset: -120 });
            }
        },

        draw(newDraw) {
            if(newDraw) {
                this.listen(newDraw);
            }
        }
    },

    created: function() {
        this.addParticipant();
        this.addParticipant();
        this.addParticipant();
    },

    methods: {
        otherParticipants(idx) {
            var participants = this.form.participants.map((participant, idx) => {participant.idx = idx; return participant;});
            participants.splice(idx, 1);
            return participants.filter(participant => !!participant.name);
        },

        addParticipant(name, email, exclusions) {
            var n = this.form.participants.push({
                name: name?.trim() || '',
                email: email?.trim() || '',
                id: 'id' + this.form.participants.length + new Date().getTime(),
                exclusions: []
            });

            // Delay to wait for the names of all other participants to be set
            if(exclusions) {
                setTimeout(
                    () => {
                        this.form.participants[n - 1].exclusions = (exclusions || '')
                            .split(',')
                            .map(s => s.trim())
                            .map(exclusion => {
                                var idx = this.form.participants.findIndex(participant => (participant.name === exclusion));
                                return idx !== -1 ? idx : undefined;
                            })
                            .filter(p => p !== '');
                    },
                    0
                );
            }
        },

        importParticipants(file, encoding) {
            this.importing = true;
            Papa.parse(file, {
                encoding,
                error: function() {
                    this.importing = false;
                    this.$dialog.alert(this.$t('form.csv.importError'));
                },
                complete: file => {
                    this.form.participants.splice(0);

                    // Set participants
                    file.data.forEach(
                        function(participant) {
                            if (participant[0] !== '' && participant.length >= 2) {
                                this.addParticipant(participant[0], participant[1], participant[2] || '');
                            }
                        }.bind(this)
                    );

                    if (this.form.participants.length < 3) {
                        for (var i = 0; i < 3 - this.form.participants.length; i++) {
                            this.addParticipant();
                        }
                    }

                    this.importing = false;

                    Toastify.success(this.$t('form.csv.importSuccess'));
                }
            });
        },

        submit() {


        },

        reset() {
            this.form.participants = [];
            this.form.title = '';
            this.form.content = '';

            this.draw = null;
            this.draw_status = 'created';

            this.addParticipant();
            this.addParticipant();
            this.addParticipant();
        },

        success(data) {
            this.draw = data.draw;
        },

        listen(draw) {
            Echo.channel('pending_draw.'+draw)
                .listen('.status.update', data => {
                    this.draw_status = data.status;
                });
        }
    }
}).use(i18n).mount('#randomForm');
