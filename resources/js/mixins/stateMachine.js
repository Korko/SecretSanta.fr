export default {
    data() {
        return {
            data: null,
            states: {},
            previousState: null,
            state: null
        };
    },
    methods: {
        send(trigger, data) {
            this.data = data;

            var newState = this.states[this.state][trigger] || this.states[this.state]['*'];
            if (newState) {
                this.previousState = this.state;
                this.state = newState;

                (this['state' + this.state[0].toUpperCase() + this.state.slice(1)] || function() {})();
            }
        }
    }
};
