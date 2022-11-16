<script>
    import NavElement from '@/Components/NavElement.vue';

    export default {
        components: {
            NavElement
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
        <a v-bind="$attrs"><slot /></a>
    </NavElement>
</template>

<style scoped>
    a[href]:not(:empty)::before {
        content: "↪";
    }
    a[href^="#"]:not(:empty)::before {
        content: "#";
    }
    a[href^="mailto:"]:not(:empty)::before {
        content: "@";
    }
</style>