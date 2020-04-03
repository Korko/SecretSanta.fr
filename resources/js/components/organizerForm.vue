<script>
    import { mapState } from 'vuex';

    import InputEdit from './inputEdit.vue';

    import DefaultForm from './form.vue';

    export default {
        components: { InputEdit },
        extends: DefaultForm,
        computed: {
            checkUpdates() {
                return !!Object.values(this.data.participants).find(
                    participant => participant.delivery_status === 'created'
                );
            },
            ...mapState(['csrf', 'key'])
        },
        created() {
            setInterval(() => {
                if (this.checkUpdates) this.fetchState();
            }, 5000);
        },
        methods: {
            update(k, data) {
                this.data.participants[k].address = data.value;
                this.data.participants[k].mail.delivery_status = data.participant.mail.delivery_status;
                this.data.participants[k].mail.updated_at = data.participant.mail.updated_at;
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
            }
        }
    };
</script>

<template>
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
                        :value="participant.address"
                        type="email"
                        name="email"
                        @update="update(k, $event)"
                    />
                </td>
                <td>{{ participant.mail.delivery_status }}</td>
            </tr>
        </tbody>
    </table>
</template>
