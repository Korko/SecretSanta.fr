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
            <tr v-for="participant in data.participants">
                <td>{{ participant.name }}</td>
                <td>
                    <input-edit
                        :action="`/org/${data.draw}/${participant.id}/changeEmail`"
                        v-model="participant.email_address"
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
        computed: mapState(['lang'])
    };
</script>
