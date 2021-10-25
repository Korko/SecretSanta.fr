<script>
export default {
    props: {

    },
    data() {
        return {

        };
    },
    created() {
        var data = JSON.parse(window.localStorage.getItem('secretsanta')) || {};

        console.log(data);
    },
    computed: {
    },
    watch: {
    }
}
</script>

<template>
    <div>
        test
    </div>
</template>

<style>
    @import "../../sass/layout.scss";

</style>
