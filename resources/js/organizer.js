import Vue from 'vue';
import OrganizerFetcher from './components/organizerFetcher.js';
window.app = new Vue({
    mixins: [OrganizerFetcher]
});
