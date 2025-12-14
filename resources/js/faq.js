import Faq from './components/faq.vue';

window.app = new Vue({
    el: '#content',
    components: {
        Faq
    },
    mounted: function() {
        document.body.classList.add('cssLoading');
        setTimeout(() => document.body.classList.remove('cssLoading'), 0);
    }
});
