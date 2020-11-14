<script>
    import jQuery from 'jquery';

    import Vue from "vue";

    import VuejsDialog from "vuejs-dialog";
    import 'vuejs-dialog/dist/vuejs-dialog.min.css';
    Vue.use(VuejsDialog);

    import store from '../partials/store.js';

    import { required, email } from 'vuelidate/lib/validators';

    import InputEdit from './inputEdit.vue';
    import DefaultForm from './form.vue';

    export default {
        components: { InputEdit },
        extends: DefaultForm,
        props: {
            data: {
                type: Object,
                default() { return {}; }
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
                var app = this;
                return jQuery.ajax({
                    url: `/org/${this.data.draw}/fetchState`,
                    type: 'POST',
                    data: { _token: this.csrf, key: this.key },
                    success(data) {
                        if (data.participants) {
                            Object.values(data.participants).forEach(participant => {
                                var new_update = new Date(participant.mail.updated_at);
                                var old_update = new Date(app.data.participants[participant.id].mail.updated_at);
                                app.data.participants[participant.id].mail.delivery_status =
                                    new_update > old_update
                                        ? participant.mail.delivery_status
                                        : app.data.participants[participant.id].mail.delivery_status;
                            });
                        }
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
		var app = this;
                return jQuery.ajax({
                    url: `/org/${this.data.draw}/`,
                    type: 'DELETE',
                    data: { _token: this.csrf, key: this.key },
                    success(data) {
                        app.$dialog.alert(data.message)
                            .then(() => window.location.pathname = '/');
                    }
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
                            :action="`/org/${data.draw}/${participant.id}/changeEmail`"
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
        <button type="button" class="btn btn-danger" @click="confirmPurge">{{ $t('organizer.purge.button') }}</button>
    </div>
</template>
