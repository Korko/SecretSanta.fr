<script>
    import fetch from '@/Modules/fetch.js';

    export default {
        props: {
            src: {
                type: String,
                required: true
            }
        },
        async setup(props) {
            return {
                data: await fetch(props.src)
            };
        }
    }
</script>

<template>
    <slot name="default" v-bind="data" />
</template>
