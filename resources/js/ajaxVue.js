var $ = require('jquery');
var alertify = require('alertify.js');

export default {
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
    call: function(url, options) {
      if(!this.sending && !this.sent) {
        this.sending = true;
        var app = this;
        $.ajax({
          url : url,
          type: options.data ? "POST" : "GET",
          data : options.data,
          success: function(data, textStatus, jqXHR) {
            if(jqXHR.responseJSON && jqXHR.responseJSON.message)
              alertify.success(jqXHR.responseJSON.message);

            app.sending = false;
            app.sent = true;

            if(options.success) options.success(jqXHR.responseJSON);
          },
          error: function(jqXHR, textStatus, errorThrown) {
            if(jqXHR.responseJSON && jqXHR.responseJSON.message)
              alertify.error(jqXHR.responseJSON.message);
            if(jqXHR.responseJSON && jqXHR.responseJSON.errors)
              app.fieldErrors = jqXHR.responseJSON.errors;

            app.sending = false;

            if(options.error) options.error(jqXHR.responseJSON);
          }
        });
      }
    },
    submit: function(event) {
      this.submitForm(event.target);
    },
    submitForm: function(target, options) {
      var postData = $(target).serializeArray();
      var formUrl = $(target).attr("action");
      this.call(formUrl, Object.assign({
        data: postData
      }, options));
    }
  }
};
