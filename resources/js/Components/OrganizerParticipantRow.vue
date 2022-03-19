<script>
    import { useVuelidate } from '@vuelidate/core';
    import { required, email, maxLength } from '@vuelidate/validators';

    import InputEdit from './InputEdit.vue';
    import EmailStatus from './EmailStatus.vue';

    export default {
        components: {
            InputEdit,
            EmailStatus
        },
        setup: () => ({ v$: useVuelidate() }),
        validations: () => ({
            name: {
                required,
                maxLength: maxLength(55),
                unique(value) {
                    // standalone validator ideally should not assume a field is required
                    if (value === '') return true;

                    return (Object.values(this.participants).filter(participant => (participant.name === value)).length === 1);
                }
            },
            email: {
                required,
                maxLength: maxLength(320),
                format: email
            }
        }),
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
                :validation="v$.name"
                :submit="updateName"
                :disabled="finished"
            >
                <template #errors>
                    <div v-if="!v$.name.required" class="invalid-tooltip">{{ $t('validation.custom.randomform.participant.name.required') }}</div>
                    <div v-else-if="!v$.name.unique" class="invalid-tooltip">{{ $t('validation.custom.randomform.participant.name.distinct') }}</div>
                </template>
            </InputEdit>
        </td>
        <td>
            <InputEdit
                :modelValue="email"
                :validation="v$.email"
                :submit="updateEmail"
                :disabled="finished"
            >
                <template #errors>
                    <div v-if="!v$.email.required" class="invalid-tooltip">{{ $t('validation.custom.organizer.email.required') }}</div>
                    <div v-else-if="!v$.email.format" class="invalid-tooltip">{{ $t('validation.custom.organizer.email.format') }}</div>
                </template>
            </InputEdit>
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