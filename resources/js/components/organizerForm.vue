<script>
    import Vue from "vue";

    import VuejsDialog from "vuejs-dialog";
    import 'vuejs-dialog/dist/vuejs-dialog.min.css';
    Vue.use(VuejsDialog);

    import { required, email } from 'vuelidate/lib/validators';

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
                    title: this.$t('organizer.purge.confirm.title', {expiration: new Date(this.data.expires_at).toLocaleString('fr-FR', {day: 'numeric', month: 'long', year: 'numeric'})}),
                    body: this.$t('organizer.purge.confirm.body')
                };

                this.$dialog.confirm(message, options)
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
                    .get(this.routes.csvUrl, {responseType: 'blob'})
                    .then(response => {
// TODO move from there to a separate method
                        var blob = new Blob([response.data]);
                        blob = blob.slice(0, blob.size, "text/csv");

                        const url = window.URL.createObjectURL(blob);
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', 'secretsanta_'+(Moment(this.data.expires_at).format('YYYY-MM-DD'))+'.csv');
                        document.body.appendChild(link);
                        link.click();
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
                <tr v-for="(participant, k) in data.participants" :key="participant.id">
                    <td>{{ participant.name }}</td>
                    <td>
                        <input-edit
                            :value="participant.email"
                            :validation="validations.email"
                            :update="(email) => updateEmail(k, email)"
                        >
                            <template #errors="{ $v: $v }">
                                <div v-if="!$v.required" class="invalid-tooltip">{{ $t('validation.custom.organizer.email.required') }}</div>
                                <div v-else-if="!$v.format" class="invalid-tooltip">{{ $t('validation.custom.organizer.email.format') }}</div>
                            </template>
                        </input-edit>
                    </td>
                    <td><email-status :delivery_status="participant.mail.delivery_status" @redo="updateEmail(k, participant.email)" /></td>
                </tr>
            </tbody>
        </table>
        <button type="button" class="btn btn-primary" @click="download">
            <i class="fas fa-download" />
            {{ $t('organizer.download.button') }}
        </button>
        <button type="button" class="btn btn-danger" @click="confirmPurge">
            <i class="fas fa-trash" />
            {{ $t('organizer.purge.button') }}
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