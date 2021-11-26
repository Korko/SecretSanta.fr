<script>
    import Vue from 'vue';

    import VuejsDialog from 'vuejs-dialog';
    Vue.use(VuejsDialog);

    import Vuelidate from 'vuelidate';
    import { required, email } from 'vuelidate/lib/validators'
    Vue.use(Vuelidate);

    import { download } from '../partials/helpers.js';

    import Moment from 'moment';

    import fetch from '../partials/fetch.js';
    import Echo from '../partials/echo.js';
    import { deepMerge } from '../partials/helpers.js';

    import InputEdit from './inputEdit.vue';
    import EmailStatus from './emailStatus.vue';
    import Tooltip from './tooltip.vue';
    import DefaultForm from './form.vue';

    export default {
        components: { InputEdit, EmailStatus, Tooltip },
        extends: DefaultForm,

        props: {
            data: {
                type: Object,
                default() { return {}; }
            },
            routes: {
                type: Object,
                required: true
            }
        },
        data() {
            return {

            };
        },
        computed: {
            checkUpdates() {
                return !!Object.values(this.data.participants).find(
                    participant => participant.mail.delivery_status !== 'error'
                );
            },
            expired() {
                return Moment(this.data.draw.expires_at).isBefore(Moment(), "day");
            },
            expirationDateShort() {
                return Moment(this.data.draw.expires_at).format('YYYY-MM-DD');
            },
            expirationDateLong() {
                return new Date(this.data.draw.expires_at).toLocaleString('fr-FR', {day: 'numeric', month: 'long', year: 'numeric'});
            },
            deletionDateLong() {
                return new Date(this.data.draw.deleted_at).toLocaleString('fr-FR', {day: 'numeric', month: 'long', year: 'numeric'});
            }
        },

        validations: {
            name: {
                required,
                unique(value) {
                    // standalone validator ideally should not assume a field is required
                    if (value === '') return true;

                    return (Object.values(this.data.participants).filter(participant => (participant.name === value)).length === 1);
                }
            },
            email: {
                required,
                format: email
            }
        },

        created() {
            Echo.channel('draw.'+this.data.draw.hash)
                .listen('.pusher:subscription_succeeded', () => {
                    this.fetchState();
                })
                .listen('.mail.update', data => {
                    var key = Object.keys(this.data.participants).find(key => this.data.participants[key].mail.id === data.id);

                    if(key) {
                        this.$set(this.data.participants[key].mail, 'delivery_status', data.delivery_status);
                        this.$set(this.data.participants[key].mail, 'updated_at', data.updated_at);
                    }
                });

            window.localStorage.setItem('secretsanta', JSON.stringify(deepMerge(
                JSON.parse(window.localStorage.getItem('secretsanta')) || {},
                {
                    [this.data.draw.hash]: {
                        title: this.data.draw.mail_title,
                        creation: this.data.draw.created_at,
                        expiration: this.data.draw.expires_at,
                        organizerName: this.data.organizer,
                        links: {
                            org: {link: window.location.href}
                        }
                    }
                }
            )));
        },

        methods: {
            update(k, data) {
                this.data.participants[k].email = data.value;
                this.data.participants[k].mail.delivery_status = data.participant.mail.delivery_status;
                this.data.participants[k].mail.updated_at = data.participant.mail.updated_at;
            },
            fetchState() {
                return fetch(this.routes.fetchStateUrl)
                    .then(response => {
                        if (response.participants) {
                            Object.values(response.participants).forEach(participant => {
                                var new_update = new Date(participant.mail.updated_at);
                                var old_update = new Date(this.data.participants[participant.hash].mail.updated_at);
                                this.data.participants[participant.hash].mail.delivery_status =
                                    new_update > old_update
                                        ? participant.mail.delivery_status
                                        : this.data.participants[participant.hash].mail.delivery_status;
                            });
                        }
                    });
            },
            confirmPurge() {
                let options = {
                    okText: this.$t('organizer.purge.confirm.ok'),
                    cancelText: this.$t('organizer.purge.confirm.cancel'),
                    verification: this.$t('organizer.purge.confirm.value'),
                    verificationHelp: this.$t('organizer.purge.confirm.help'),
                    type: 'hard',
                    customClass: 'purge'
                };

                let message = {
                    title: this.$t('organizer.purge.confirm.title', {deletion: this.deletionDateLong}),
                    body: ''
                };
                if(this.data.finalCsvAvailable && !this.expired) {
                    message.body = this.$t('organizer.purge.confirm.body_final'); // Won't be able to download final recap + dearSanta
                } else if(this.expired) {
                    message.body = this.$t('organizer.purge.confirm.body_expired'); // Won't be able to download recap anymore
                } else {
                    message.body = this.$t('organizer.purge.confirm.body_nofinal'); // Won't be able to download recap anymore + DearSanta
                }

                this.$dialog
                    .confirm(message, options)
                    .then(this.purge);
            },
            purge() {
                return fetch(this.routes.deleteUrl, 'DELETE')
                    .then(data => {
                        this.$dialog
                            .alert(data.message)
                            .then(() => window.location.pathname = '/');
                    });
            },
            updateEmail(k, email) {
                this.$set(this.data.participants[k], 'email', email);
                this.$set(this.data.participants[k].mail, 'delivery_status', 'created');

                return fetch(this.data.changeEmailUrls[this.data.participants[k].hash], 'POST', {
                    email: email
                });
            },
            download() {
                fetch(this.routes.csvInitUrl, 'GET', '', {responseType: 'blob'})
                    .then(response => {
                        download(response, 'secretsanta_'+this.expirationDateShort+'_init.csv', 'text/csv');
                    });
            },
            downloadPlus() {
                fetch(this.routes.csvFinalUrl, 'GET', '', {responseType: 'blob'})
                    .then(response => {
                        download(response, 'secretsanta_'+this.expirationDateShort+'_full.csv', 'text/csv');
                    });
            }
        }
    };
</script>

<template>
    <div>
        <div v-if="expired" class="alert alert-warning" role="alert">
            {{ $t('organizer.expired', {expires_at: expirationDateLong}) }}
        </div>
        <table class="table table-hover">
            <caption>{{ $t('organizer.list.caption') }}</caption>
            <thead>
                <tr class="table-active">
                    <th scope="col">
                        {{ $t('organizer.list.name') }}
                    </th>
                    <th scope="col">
                        {{ $t('organizer.list.email') }}
                    </th>
                    <th scope="col">
                        {{ $t('organizer.list.status') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(participant, k) in data.participants" :key="participant.hash">
                    <td>
                        <input-edit
                            :value="participant.name"
                            :validation="$v.name"
                            :update="(name) => updateName(k, name)"
                            :disabled="expired"
                        >
                            <template #errors>
                                <div v-if="!$v.name.required" class="invalid-tooltip">{{ $t('validation.custom.randomform.participant.name.required') }}</div>
                                <div v-else-if="!$v.name.unique" class="invalid-tooltip">{{ $t('validation.custom.randomform.participant.name.distinct') }}</div>
                            </template>
                        </input-edit>
                    </td>
                    <td>
                        <input-edit
                            :value="participant.email"
                            :validation="$v.email"
                            :update="(email) => updateEmail(k, email)"
                            :disabled="expired"
                        >
                            <template #errors>
                                <div v-if="!$v.email.required" class="invalid-tooltip">{{ $t('validation.custom.organizer.email.required') }}</div>
                                <div v-else-if="!$v.email.format" class="invalid-tooltip">{{ $t('validation.custom.organizer.email.format') }}</div>
                            </template>
                        </input-edit>
                    </td>
                    <td><email-status :delivery_status="participant.mail.delivery_status" :last_update="participant.mail.updated_at" :disabled="expired" @redo="updateEmail(k, participant.email)" /></td>
                </tr>
            </tbody>
        </table>
        <button type="button" class="btn btn-danger" @click="confirmPurge">
            <i class="fas fa-trash" />
            {{ $t('organizer.purge.button') }}
        </button>
        <template v-if="data.finalCsvAvailable">
            <tooltip direction="right">
                <template #tooltip>
                    <picture>
                        <source srcset="../../images/rune-haugseng-UCzjZPCGV1Y-unsplash.webp" type="image/webp" />
                        <source srcset="../../images/rune-haugseng-UCzjZPCGV1Y-unsplash.jpg" type="image/jpg" />
                        <img class="media-object" src="../../images/rune-haugseng-UCzjZPCGV1Y-unsplash.jpg" />
                    </picture>
                    <div class="text-content">
                        <h3>{{ $t('organizer.download.button_initial-tooltip.title') }}</h3>
                        <p>{{ $t('organizer.download.button_initial-tooltip.content') }}</p>
                    </div>
                </template>
                <template #default>
                    <button type="button" class="btn btn-primary" @click="download">
                        <i class="fas fa-download" />
                        {{ $t('organizer.download.button_initial') }}
                    </button>
                </template>
            </tooltip>
            <tooltip direction="right">
                <template #tooltip>
                    <picture>
                        <source srcset="../../images/mike-arney-9r-_2gzP37k-unsplash.webp" type="image/webp" />
                        <source srcset="../../images/mike-arney-9r-_2gzP37k-unsplash.jpg" type="image/jpg" />
                        <img class="media-object" src="../../images/mike-arney-9r-_2gzP37k-unsplash.jpg" />
                    </picture>
                    <div class="text-content">
                        <h3>{{ $t('organizer.download.button_final-tooltip.title') }}</h3>
                        <p>{{ $t('organizer.download.button_final-tooltip.explain') }}</p>
                        <p class="border border-white border-1 rounded pl-2 pr-2 font-italic">{{ $t('organizer.download.button_final-tooltip.limit', {expires_at: expirationDateLong, deleted_at: deletionDateLong}) }}</p>
                    </div>
                </template>
                <template #default>
                    <button :disabled="!expired" type="button" class="btn btn-primary" @click="downloadPlus">
                        <i class="fas fa-download" />
                        {{ $t('organizer.download.button_final') }}
                    </button>
                </template>
            </tooltip>
        </template>
        <tooltip v-else direction="right">
            <template #tooltip>
                <picture>
                    <source srcset="../../images/rune-haugseng-UCzjZPCGV1Y-unsplash.webp" type="image/webp" />
                    <source srcset="../../images/rune-haugseng-UCzjZPCGV1Y-unsplash.jpg" type="image/jpg" />
                    <img class="media-object" src="../../images/rune-haugseng-UCzjZPCGV1Y-unsplash.jpg" />
                </picture>
                <div class="text-content">
                    <h3>{{ $t('organizer.download.button-tooltip.title') }}</h3>
                    <p>{{ $t('organizer.download.button-tooltip.content') }}</p>
                </div>
            </template>
            <template #default>
                <button type="button" class="btn btn-primary" @click="download">
                    <i class="fas fa-download" />
                    {{ $t('organizer.download.button') }}
                </button>
            </template>
        </tooltip>
    </div>
</template>

<style>
    @import "~vuejs-dialog/dist/vuejs-dialog.min.css";

    .table td {
        vertical-align: middle !important;
    }

    table caption {
        display: none;
    }

    .purge .dg-btn--ok {
      color: #a82824;
      background-color: #fefefe;
      border-color: #a82824;
    }
</style>
