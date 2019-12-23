<template>
    <div>
        <div v-if="edit">
            <div class="input-group">
                <input v-bind="$attrs" v-model="newValue" class="form-control" />
                <div class="input-group-append">
                    <button type="button" class="btn btn-success" @click="validateEdit"><i class="fas fa-check-circle"></i></button>
                    <button type="button" class="btn btn-danger" @click="cancelEdit"><i class="fas fa-times-circle"></i></button>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="input-group">
                <input :value="newValue" class="form-control" disabled />
                <div class="input-group-append">
                    <button type="button" class="btn btn-secondary" @click="startEdit"><i class="fas fa-user-edit"></i></button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    inheritAttrs: false,
    props: ['value'],
    data() {
        return {
            edit: false,
            newValue: this.value
        };
    },
    methods: {
        startEdit() {
            this.edit = true;
        },
        validateEdit() {
            // TODO validate if needed
            this.edit = false;
            this.$emit('value', this.newValue);
        },
        cancelEdit() {
            this.edit = false;
            this.newValue = this.value;
        }
    }
}
</script>
