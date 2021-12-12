<template>
    <textarea v-model="currentValue" :style="inputStyle" v-bind="$attrs"></textarea>
    <textarea class="shadow" v-model="currentValue" ref="shadow" tabindex="0"></textarea>
</template>

<script>
    export default {
        props: {
            modelValue: {
                type: String,
                default: ''
            },
            minHeight: {
                type: Number,
                default: 0
            }
        },
        emits: ['update:modelValue'],
        data() {
            return {
                currentValue: this.modelValue,
                inputHeight: this.minHeight
            };
        },
        watch: {
            modelValue() {
                this.currentValue = this.modelValue;
            },
            currentValue() {
                this.resize();
                this.$emit('update:modelValue', this.currentValue);
            }
        },
        computed: {
            inputStyle () {
                return {
                    'min-height': this.inputHeight
                };
            }
        },
        mounted () {
            this.resize();
        },
        methods: {
            resize () {
                this.$nextTick(() => {
                    this.inputHeight = Math.max(this.minHeight, this.$refs.shadow.scrollHeight)+'px';
                });
            }
        }
    }
</script>

<style scoped lang="scss">
    textarea {
        padding: 8px;
        border: 1px solid #aeaeae;
        resize: none;
        overflow: hidden;
        font-size: 16px;
        height: 0;

        &.shadow {
            max-height: 0;
            pointer-events: none;
            opacity: 0;
            margin: 0;
            position: absolute;
        }
    }
</style>