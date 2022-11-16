<script>
    import fetch from '@/Modules/fetch.js';

    export default {
        props: {
            route: {
                type: String,
                required: true
            }
        },
        async setup(props) {
            return {
                data: await fetch(props.route)
            };
        }
    }
</script>

<template>
    <slot name="default" v-bind="data" />
</template>