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

    import Lang from '../partials/lang.js';

    import InputEdit from './inputEdit.vue';

    import DefaultForm from './form.vue';

    export default {
        extends: DefaultForm,
        components: { InputEdit },
        computed: mapState(['lang']),
        methods: {
            update(k, data) {
                this.data.participants[k].email_address = data.value;
                this.data.participants[k].delivery_status = data.status;
            }
        }
    };
</script>
