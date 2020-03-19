import Vue from 'vue';
import RandomForm from './components/randomForm.vue';
import store from './partials/store.js';

window.app = new Vue({
    el: '#form',

    components: {
        RandomForm
    },

    store
});
