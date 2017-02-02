var $ = require('jquery');
var alertify = require('alertify.js');

var Vue = require('vue');

module.exports = Vue.extend({
  data: function() {
    return {
      fieldErrors: [],
      sending: false,
      sent: false
    };
  },
  computed: {
    errors: function() {
      var errors = [];
      for(var field in this.fieldErrors) {
        errors = errors.concat(this.fieldErrors[field]);
      }
      return errors;
    }
  },
  methods: {
    submit: function(event) {
      var postData = $(event.target).serializeArray();
      var formURL = $(event.target).attr("action");
      if(!this.sending && !this.sent) {
        this.sending = true;

        var app = this;
        $.ajax({
          url : formURL,
          type: "POST",
          data : postData,
          success: function(data, textStatus, jqXHR) {
            alertify.alert(jqXHR.responseJSON.message);
            app.sent = true;
          },
          error: function(jqXHR, textStatus, errorThrown) {
            app.fieldErrors = jqXHR.responseJSON;
            app.sending = false;
          }
        });
      }
    }
  }
});
