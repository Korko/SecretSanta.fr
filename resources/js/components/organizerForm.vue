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
                    <ajax-form :action="`/org/${data.draw.id}/${participant.id}/changeEmail`" v-slot="{ submit }">
                        <input-edit
                            v-model="participant.email_address"
                            :update="submit"
                            type="email"
                        ></input-edit>
                    </ajax-form>
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
    import AjaxForm from './ajaxForm.vue';

    import DefaultForm from './form.vue';

    export default {
        extends: DefaultForm,
        components: { InputEdit, AjaxForm },
        computed: mapState(['lang'])
    };
</script>
