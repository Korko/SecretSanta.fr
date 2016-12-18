var $ = require('jquery');
var alertify = require('alertify.js');
var SmsTools = require('./smsTools.js');

var Vue = require('vue');
var VueAutosize = require('vue-autosize');
Vue.use(VueAutosize);

window.app = new Vue({
  el: '#form',

  data: {
    sending: false,
    sent: false,
    participants: [],
    fieldErrors: {},
    smsContent: '',
    maxSms: global.maxSms
  },

  components: {
    participant: {
        template: '#participant-template',
        props: ['idx', 'participants'],
        data: function() {
            return {
                name: '',
                email: '',
                phone: '',
            };
        },
        computed: {
            participantNames: function() {
                var names = [];
                this.participants.forEach(function(participant, idx) {
                    if(participant.name && idx !== this.idx) {
                        names.push({id: participant.id, value: idx, text: participant.name});
                    }
                }.bind(this));
                return names;
            }
        },
        watch: {
            name: function() {
                this.$emit('changename', this.name);
            },
            email: function() {
                this.$emit('changeemail', this.email);
            },
            phone: function() {
                this.$emit('changephone', this.phone);
            }
        }
    }
  },

  computed: {

    emailUsed: function() {
      var used = false;
      for(var i in this.participants) {
        used = used || (this.participants[i].email !== '');
      }
      return used;
    },

    phoneUsed: function() {
      var used = false;
      for(var i in this.participants) {
        used = used || (this.participants[i].phone !== '');
      }
      return used;
    },

    errors: function() {
      var errors = [];
      for(var field in this.fieldErrors) {
        errors = errors.concat(this.fieldErrors[field]);
      }
      return errors;
    },

    smsCount: function() {
      return Math.min(SmsTools.chunk(this.smsContent).length, this.maxSms);
    },

    charactersLeft: function() {
      return SmsTools.chunkMaxLength(this.smsContent, this.smsCount, true) - this.smsContent.length;
    },

    maxLength: function() {
      return SmsTools.chunkMaxLength(this.smsContent, this.maxSms, true);
    }

  },

  created: function() {
    this.addParticipant();
    this.addParticipant();
  },

  watch: {
    sending: function(newVal) {
      // If we reset the sending status, reset the captcha
      if(!newVal) {
        grecaptcha.reset();
      }
    },

    sent: function(newVal) {
      // If sent is a success, scroll to the message
      if(newVal) {
        $.scrollTo('#form .row', 800, {offset: -120});
      }
    },

    errors: function(newVal) {
      // If there's new errors, scroll to them
      if(newVal.length) {
        $.scrollTo('#form .row', 800, {offset: -120});
      }
    }
  },

  methods: {

    addParticipant: function() {
      this.participants.push({
        name: '',
        email: '',
        phone: '',
        id: 'id' + this.participants.length + (new Date()).getTime()
      });
    },

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
            alertify.alert(jqXHR.responseJSON[0]);
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
