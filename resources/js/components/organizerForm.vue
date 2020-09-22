<script>
    import Vue from "vue";

    import VuejsDialog from "vuejs-dialog";
    import 'vuejs-dialog/dist/vuejs-dialog.min.css';
    Vue.use(VuejsDialog);

    import { required, email } from 'vuelidate/lib/validators';

    import axios from '../partials/axios.js';
    import store from '../partials/store.js';

    import InputEdit from './inputEdit.vue';
    import DefaultForm from './form.vue';

    export default {
        components: { InputEdit },
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
                ...store,
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
                    participant => participant.mail.delivery_status === 'created'
                );
            }
        },
        created() {
            setInterval(() => {
                if (this.checkUpdates) this.fetchState();
            }, 1000);
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
                    type: 'hard'
                };

                let message = {
                    title: this.$t('organizer.purge.confirm.title', {expiration: new Date(this.data.expires_at).toLocaleString('fr-FR', {day: 'numeric', month: 'long'})}),
                    body: this.$t('organizer.purge.confirm.body')
                };

                this.$dialog.confirm(message, options)
                    .then(this.purge);
            },
            purge() {
                return axios
                    .delete(this.routes.deleteUrl, { _token: this.csrf, key: this.key })
                    .then(data => {
                        this.$dialog
                            .alert(data.message)
                            .then(() => window.location.pathname = '/');
                    });
            }
        }
    };
</script>

<template>
    <div>
        <table class="table table-hover">
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
                            :action="data.changeEmailUrls[participant.id]"
                            :value="participant.email"
                            name="email"
                            :validation="validations.email"
                            @update="update(k, $event)"
                        >
                            <template #errors="{ $v: $v }">
                                <div v-if="!$v.required" class="invalid-tooltip">{{ $t('validation.custom.organizer.email.required') }}</div>
                                <div v-else-if="!$v.format" class="invalid-tooltip">{{ $t('validation.custom.organizer.email.format') }}</div>
                            </template>
                        </input-edit>
                    </td>
                    <td>{{ $t(`common.email.status.${participant.mail.delivery_status}`) }}</td>
                </tr>
            </tbody>
        </table>
        <button type="button" class="btn btn-danger" @click="confirmPurge">
            <i class="fas fa-trash" />
            {{ $t('organizer.purge.button') }}
        </button>
    </div>
</template>
