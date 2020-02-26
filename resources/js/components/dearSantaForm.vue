<template>
    <div>
        <ajax-form
             :action="`/dearsanta/${data.santa.id}/send`"
             :button="true"
             @success="success"
        >
            <fieldset>
                <div class="form-group">
                    <label for="mailContent">Contenu du mail</label>
                    <textarea
                        id="mailContent"
                        name="content"
                        required
                        placeholder="Cher Papa Noël..."
                        class="form-control"
                    ></textarea>
                </div>
            </fieldset>
        </ajax-form>
        <table class="table table-hover">
            <thead>
                <tr class="table-active">
                    <th scope="col">{{ lang.get('dearsanta.list.date') }}</th>
                    <th scope="col">{{ lang.get('dearsanta.list.body') }}</th>
                    <th scope="col">{{ lang.get('dearsanta.list.status') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="email in emails" class="email">
                    <td>{{ email.created_at }}</td>
                    <td>{{ email.email_body }}</td>
                    <td>{{ email.delivery_status }}</td>
                </tr>
                <tr v-if="emails.length === 0" class="no-email">
                    <td colspan="3">{{ lang.get('dearsanta.list.empty') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    import { mapState } from 'vuex';

    import Lang from '../partials/lang.js';

    import DefaultForm from './form.vue';

    export default {
        extends: DefaultForm,
        computed: {
            emails() {
                return Object.values(this.data.emails).sort(
                    (email1, email2) => (new Date(email1.created_at)) > new Date(email2.created_at) ? -1 : 1
                ).map(email => {
                    email.created_at = (new Date(email.created_at)).toLocaleString('fr-FR');
                    return email;
                });
            },
            checkUpdates() {
                return !!(
                    Object.values(this.data.emails)
                        .find(email => email.delivery_status === 'created')
                );
            },
            ...mapState(['lang'])
        },
        created() {
            setInterval(() => {
                if(this.checkUpdates) this.fetchState();
            }, 5000);
        },
        methods: {
            success(data) {
                this.$set(this.data.emails, data.email.id, data.email);
            },
            fetchState() {
                var app = this;
                return $.ajax({
                    url: `/dearsanta/${this.data.santa.id}/fetchState`,
                    type: 'POST',
                    data: { _token: this.csrf, key: this.key },
                    success(data) {
                        if (data.emails) {
                            Object.values(data.emails).forEach(email => {
                                var new_update = new Date(email.updated_at);
                                var old_update = new Date(app.data.emails[email.id].updated_at);
                                app.data.emails[email.id].delivery_status = new_update > old_update ?
                                    email.delivery_status :
                                    app.data.emails[email.id].delivery_status;
                            });
                        }
                    }
                });
            }
        }
    };
</script>
