import Failure from '../components/error.vue';
import Fetcher from '../components/fetcher.vue';
import DefaultForm from '../components/form.vue';

import StateMachine from './stateMachine.js';

export const VueFetcher = Form => {
    Form = Form || DefaultForm;

    return {
        mixins: [StateMachine],
        components: {
            Failure,
            Fetcher,
            Form
        },

        el: '#main',

        data: {
            state: 'Fetcher',
            states: Object.freeze({
                Fetcher: {
                    success: 'Form',
                    failure: 'Failure'
                }
            })
        }
    };
};

export default VueFetcher();
