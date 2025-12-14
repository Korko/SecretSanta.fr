import FixOrganizerForm from './components/fixOrganizerForm.vue';

window.app = new Vue({
    el: '#content',
    components: {
        FixOrganizerForm
    },
    mounted: function() {
        document.body.classList.add('cssLoading');
        setTimeout(() => document.body.classList.remove('cssLoading'), 0);
    }
});
