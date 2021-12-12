<script>
    export default {
        props: {
            delay: {
                type: Number,
                required: true,
                default: 100
            }
        },
        data: () => ({
            show: false
        }),
        mounted() {
            setTimeout(() => {
                this.show = true;
            }, this.delay);
        }
    };
</script>

<template>
    <span><slot v-if="show" /></span>
</template>
