<script>
    import Failure from './error.vue';
    import Fetcher from './fetcher.vue';

    import StateMachine from '../mixins/stateMachine.js';

    export default {
        mixins: [StateMachine],

        components: {
            Failure,
            Fetcher
        },

        props: {
            fetch: {
                type: String,
                required: true
            }
        },

        data: () => ({
            state: 'Fetcher',
            states: Object.freeze({
                Fetcher: {
                    success: 'Form',
                    failure: 'Failure'
                }
            })
        })
    };
</script>

<template>
    <slot v-if="state === 'Form'" :data="data">

    </slot>
    <component v-else :is="state" v-on:success="send('success', $event)" v-on:error="send('failure', $event)" :fetchUrl="fetch" v-bind="$data" />
</template>
