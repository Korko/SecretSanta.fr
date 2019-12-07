export default {
  data: function() {
    return {
      state: null,
      states: {}
    };
  },
  methods: {
    stateSuccess: function() {
      this.state = this.states[this.state].success || this.state;
    },
    stateFailure: function() {
      this.state = this.states[this.state].failure || this.state;
    }
  }
};
