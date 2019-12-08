export default {
  data: function() {
    return {
      state: null,
      states: {}
    };
  },
  methods: {
    stateSuccess: function(data) {
      this.$store.state.data = data;
      this.state = this.states[this.state].success || this.state;
    },
    stateFailure: function(data) {
      this.$store.state.data = data;
      this.state = this.states[this.state].failure || this.state;
    }
  }
};
