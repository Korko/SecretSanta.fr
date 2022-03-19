<script>
    import { Link } from '@inertiajs/inertia-vue3'
    import NavElement from './NavElement.vue';

    export default {
        components: {
            NavElement,
            Link
        },
        props: {
            class: {
                type: String
            }
        }
    }
</script>

<template>
    <NavElement :class="class">
        <Link v-bind="$attrs"><slot /></Link>
    </NavElement>
</template>

<style scoped>
    a[href]:not(:empty)::before {
        content: "↪";
    }
</style>