<script>
    import InputEdit from '@/Components/InputEdit.vue';
    import EmailStatus from '@/Components/EmailStatus.vue';

    export default {
        components: {
            InputEdit,
            EmailStatus
        },
        props: {
            name: {
                type: String,
                required: true
            },
            email: {
                type: String,
                required: true
            },
            target: {
                type: String,
                // Not required
            },
            mail: {
                type: Object,
                required: true
            },
            participants: {
                type: Object,
                required: true
            },
            finished: {
                type: Boolean,
                required: true
            },
            canWithdraw: {
                type: Boolean,
                required: true
            },
            validate: {
                type: Function,
                required: true
            },
            updateEmail: {
                type: Function,
                required: true
            },
            updateName: {
                type: Function,
                required: true
            }
        }
    }
</script>

<template>
    <tr>
        <td>
            <InputEdit
                :modelValue="name"
                :submit="updateName"
                :validate="(value) => validate(`name`, value)"
                :disabled="finished"
            />
        </td>
        <td>
            <InputEdit
                :modelValue="email"
                :submit="updateEmail"
                :validate="(value) => validate(`email`, value)"
                :disabled="finished"
            />
        </td>
        <td v-if="finished">
            {{ target }}
        </td>
        <td><EmailStatus :delivery_status="mail.delivery_status" :last_update="mail.updated_at" :disabled="finished" @redo="$emit('resend')" /></td>
        <td v-if="canWithdraw">
            <button
                type="button"
                class="btn btn-outline-danger participant-remove"
                @click="confirmWithdrawal(k)"
            >
                <i class="fas fa-minus" /><span> {{ $t('organizer.withdraw.button') }}</span>
            </button>
        </td>
    </tr>
</template>

<style>

</style>