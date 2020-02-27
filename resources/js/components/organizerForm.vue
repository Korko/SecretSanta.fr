<template>
    <table class="table table-hover">
        <thead>
            <tr class="table-active">
                <th scope="col">{{ lang.get('organizer.list.name') }}</th>
                <th scope="col">{{ lang.get('organizer.list.email') }}</th>
                <th scope="col">{{ lang.get('organizer.list.status') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(participant, k) in data.participants">
                <td>{{ participant.name }}</td>
                <td>
                    <input-edit
                        :action="`/org/${data.draw}/${participant.id}/changeEmail`"
                        :value="participant.email_address"
                        @update="update(k, $event)"
                        type="email"
                        name="email"
                    ></input-edit>
                </td>
                <td>{{ participant.delivery_status }}</td>
            </tr>
        </tbody>
    </table>
</template>

<script>
    import { mapState } from 'vuex';

    import InputEdit from './inputEdit.vue';

    import DefaultForm from './form.vue';

    export default {
        extends: DefaultForm,
        components: { InputEdit },
        computed: {
            checkUpdates() {
                return !!(
                    Object.values(this.data.participants)
                        .find(participant => participant.delivery_status === 'created')
                );
            },
            ...mapState(['csrf', 'key', 'lang'])
        },
        created() {
            setInterval(() => {
                if(this.checkUpdates) this.fetchState();
            }, 5000);
        },
        methods: {
            update(k, data) {
                this.data.participants[k].email_address = data.value;
                this.data.participants[k].delivery_status = data.participant.delivery_status;
                this.data.participants[k].updated_at = data.participant.updated_at;
            },
            fetchState() {
                var app = this;
                return $.ajax({
                    url: `/org/${this.data.draw}/fetchState`,
                    type: 'POST',
                    data: { _token: this.csrf, key: this.key },
                    success(data) {
                        if (data.participants) {
                            Object.values(data.participants).forEach(participant => {
                                var new_update = new Date(participant.updated_at);
                                var old_update = new Date(app.data.participants[participant.id].updated_at);
                                app.data.participants[participant.id].delivery_status = new_update > old_update ?
                                    participant.delivery_status :
                                    app.data.participants[participant.id].delivery_status;
                            });
                        }
                    }
                });
            }
        }
    };
</script>
