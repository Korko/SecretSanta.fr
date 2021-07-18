<script>
    import Vue from "vue";

    import VuejsDialog from "vuejs-dialog";
    import 'vuejs-dialog/dist/vuejs-dialog.min.css';
    Vue.use(VuejsDialog);

    import { required, email } from 'vuelidate/lib/validators';

    import { download } from '../partials/helpers.js';

    import Moment from 'moment';

    import axios from '../partials/axios.js';
    import Echo from '../partials/echo.js';

    import InputEdit from './inputEdit.vue';
    import EmailStatus from './emailStatus.vue';
    import DefaultForm from './form.vue';

    export default {
        components: { InputEdit, EmailStatus },
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
                validations: {
                    email: {
                        required,
                        format: email
                    }
                }
            };
        },
        computed: {
            checkUpdates() {
                return !!Object.values(this.data.participants).find(
                    participant => participant.mail.delivery_status !== 'error'
                );
            },
            expired() {
                return Moment(this.data.expires_at).isBefore(Moment(), "day");
            },
            expirationDateShort() {
                return Moment(this.data.expires_at).format('YYYY-MM-DD');
            },
            expirationDateLong() {
                return new Date(this.data.expires_at).toLocaleString('fr-FR', {day: 'numeric', month: 'long', year: 'numeric'});
            },
            deletionDateLong() {
                return new Date(this.data.deleted_at).toLocaleString('fr-FR', {day: 'numeric', month: 'long', year: 'numeric'});
            }
        },
        created() {
            Echo.channel('draw.'+this.data.draw)
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
        },
        methods: {
            update(k, data) {
                this.data.participants[k].email = data.value;
                this.data.participants[k].mail.delivery_status = data.participant.mail.delivery_status;
                this.data.participants[k].mail.updated_at = data.participant.mail.updated_at;
            },
            fetchState() {
                return axios
                    .get(this.routes.fetchStateUrl)
                    .then(response => {
                        if (response.data.participants) {
                            Object.values(response.data.participants).forEach(participant => {
                                var new_update = new Date(participant.mail.updated_at);
                                var old_update = new Date(this.data.participants[participant.id].mail.updated_at);
                                this.data.participants[participant.id].mail.delivery_status =
                                    new_update > old_update
                                        ? participant.mail.delivery_status
                                        : this.data.participants[participant.id].mail.delivery_status;
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
                return axios
                    .delete(this.routes.deleteUrl)
                    .then(data => {
                        this.$dialog
                            .alert(data.message)
                            .then(() => window.location.pathname = '/');
                    });
            },
            updateEmail(k, email) {
                this.$set(this.data.participants[k], 'email', email);
                this.$set(this.data.participants[k].mail, 'delivery_status', 'created');

                return axios
                    .post(this.data.changeEmailUrls[this.data.participants[k].id], {
                        email: email
                    });
            },
            download() {
                axios
                    .get(this.routes.csvInitUrl, {responseType: 'blob'})
                    .then(response => {
                        if(response.data)
                            download(response.data, 'secretsanta_'+this.expirationDateShort+'_init.csv', 'text/csv');
                    });
            },
            downloadPlus() {
                axios
                    .get(this.routes.csvFinalUrl, {responseType: 'blob'})
                    .then(response => {
                        if(response.data)
                            download(response.data, 'secretsanta_'+this.expirationDateShort+'_full.csv', 'text/csv');
                    });
            }
        }
    };
</script>

<template>
    <div>
        <div class="alert alert-warning" role="alert">
            Les adresses @laposte.net et @sfr.fr ne fonctionnent malheureusement pas bien avec SecretSanta.fr en ce moment. Les destinataires ne reçoivent pas leurs emails.<br />Autant que possible, évitez d'utilisez ces adresses.
        </div>
        <div v-if="expired" class="alert alert-warning" role="alert">
            Votre évènement est passé ({{ expirationDateLong }}). Certaines actions ne sont plus disponibles, comme réenvoyer le nom de la cible à un participant.
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
                    <td>{{ participant.name }}</td>
                    <td>
                        <input-edit
                            :value="participant.email"
                            :validation="validations.email"
                            :update="(email) => updateEmail(k, email)"
                            :disabled="expired"
                        >
                            <template #errors="{ $v: $v }">
                                <div v-if="!$v.required" class="invalid-tooltip">{{ $t('validation.custom.organizer.email.required') }}</div>
                                <div v-else-if="!$v.format" class="invalid-tooltip">{{ $t('validation.custom.organizer.email.format') }}</div>
                            </template>
                        </input-edit>
                    </td>
                    <td><email-status :delivery_status="participant.mail.delivery_status" :disabled="expired" @redo="updateEmail(k, participant.email)" /></td>
                </tr>
            </tbody>
        </table>
        <button type="button" class="btn btn-danger" @click="confirmPurge">
            <i class="fas fa-trash" />
            {{ $t('organizer.purge.button') }}
        </button>
        <template v-if="data.finalCsvAvailable">
            <button type="button" class="btn btn-primary" @click="download" v-tooltip.top="{ img: 'rune-haugseng-UCzjZPCGV1Y-unsplash.jpg', text: $t('organizer.download.button_initial-tooltip') }">
                <i class="fas fa-download" />
                {{ $t('organizer.download.button_initial') }}
            </button>
            <button :disabled="!expired" type="button" class="btn btn-primary" @click="downloadPlus" v-tooltip.top="{ img: 'mike-arney-9r-_2gzP37k-unsplash.jpg', text: $t('organizer.download.button_final-tooltip', {expires_at: expirationDateLong, deleted_at: deletionDateLong}) }">
                <i class="fas fa-download" />
                {{ $t('organizer.download.button_final') }}
            </button>
        </template>
        <button v-else type="button" class="btn btn-primary" @click="download" v-tooltip.top="{ img: 'rune-haugseng-UCzjZPCGV1Y-unsplash.jpg', text: $t('organizer.download.button-tooltip') }">
            <i class="fas fa-download" />
            {{ $t('organizer.download.button') }}
        </button>
    </div>
</template>

<style>
.purge .dg-btn--ok {
  color: #a82824;
  background-color: #fefefe;
  border-color: #a82824;
}
</style>
