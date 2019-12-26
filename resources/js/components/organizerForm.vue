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
                        v-model="participant.email_address"
                        :update="email => updateEmail(participant.id, email)"
                        type="email"
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
        methods: {
            updateEmail(id, email) {
                return $.ajax({
                    url: `/org/${this.data.draw}/${id}/changeEmail`,
                    type: 'POST',
                    data: {
                        _token: this.csrf,
                        key: this.key,
                        email: email
                    },
                    // jQuery is forcing "this" so ES6 arrow is not working here
                    success: function() {
                        this.data.participants.find(participant => {
                            return (participant.id = id);
                        }).email_address = email;
                    }.bind(this)
                });
            }
        },
        computed: mapState(['csrf', 'key', 'lang'])
    };
</script>
