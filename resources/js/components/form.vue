<template>
    <ajax-form></ajax-form>
</template>

<script>
    import { mapState } from 'vuex';

    import AjaxForm from './ajaxForm.vue';

    export default {
        components: {AjaxForm},
        props: ['data'],
        computed: mapState(['csrf', 'key'])
    };
</script>
