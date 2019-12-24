<template>
    <div></div>
</template>

<script>
    import { mapState } from 'vuex';

    import VueAjax from '../mixins/ajaxVue.js';

    export default {
        mixins: [VueAjax],
        props: ['data'],
        computed: mapState(['csrf', 'key'])
    };
</script>
