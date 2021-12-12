<template>
    <tr>
        <td>
            <input-edit
                :modelValue="name"
                :validation="v$.name"
                @update="$emit('update:name', $event)"
                :disabled="expired"
            >
                <template #errors>
                    <div v-if="!v$.name.required" class="invalid-tooltip">{{ $t('validation.custom.randomform.participant.name.required') }}</div>
                    <div v-else-if="!v$.name.unique" class="invalid-tooltip">{{ $t('validation.custom.randomform.participant.name.distinct') }}</div>
                </template>
            </input-edit>
        </td>
        <td>
            <input-edit
                :modelValue="email"
                :validation="v$.email"
                @update="$emit('update:email', event)"
                :disabled="expired"
            >
                <template #errors>
                    <div v-if="!v$.email.required" class="invalid-tooltip">{{ $t('validation.custom.organizer.email.required') }}</div>
                    <div v-else-if="!v$.email.format" class="invalid-tooltip">{{ $t('validation.custom.organizer.email.format') }}</div>
                </template>
            </input-edit>
        </td>
        <td><email-status :delivery_status="mail.delivery_status" :last_update="mail.updated_at" :disabled="expired" @redo="updateEmail(k, email)" /></td>
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

<script>
    import { useVuelidate } from '@vuelidate/core';
    import { required, email } from '@vuelidate/validators';

    import InputEdit from '../inputEdit.vue';
    import EmailStatus from '../emailStatus.vue';

    export default {
        components: {
            InputEdit,
            EmailStatus
        },
        setup: () => ({ v$: useVuelidate() }),
        validations: () => ({
            name: {
                required,
                unique(value) {
                    // standalone validator ideally should not assume a field is required
                    if (value === '') return true;

                    return (Object.values(this.participants).filter(participant => (participant.name === value)).length === 1);
                }
            },
            email: {
                required,
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
            mail: {
                type: Object,
                required: true
            },
            participants: {
                type: Object,
                required: true
            },
            expired: {
                type: Boolean,
                required: true
            },
            canWithdraw: {
                type: Boolean,
                required: true
            }
        }
    }
</script>

<style>

</style>