<script>
    export default {
        inheritAttrs: false,
        props: {
            class: {
                type: String
            }
        }
    }
</script>

<template>
    <li :class="class">
        <slot v-bind="$attrs" />
    </li>
</template>

<style scoped>
    li :slotted(a) {
        -webkit-transition: all 0.4s;
        -moz-transition: all 0.4s;
        -o-transition: all 0.4s;
        transition: all 0.4s;
        color: white;
        font-weight: bold;
        padding: 15px;
        line-height: 20px;
        display: block;
    }

    li :slotted(a[href]:not(:empty)::before) {
        padding-right: .2em;
        color: #ccc;
        display: inline-block;
        opacity: .4;
    }
    li :slotted(a[href]:not(:empty):hover::before) {
        text-decoration: none;
        color: #333;
    }

    li :slotted(a:hover) {
        background: #a00;
        color: white;
        text-shadow: none;
    }
</style>