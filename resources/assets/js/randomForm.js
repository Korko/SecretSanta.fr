var $ = require('jquery');
var alertify = require('alertify.js');
var SmsTools = require('./smsTools.js');

var Vue = require('vue');
var VueAutosize = require('vue-autosize');
Vue.use(VueAutosize);

var VueAjax = require('./ajaxVue.js');

window.app = new VueAjax({
  el: '#form',

  data: {
    participants: [],
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
          exclusions: []
        };
      },
      components: {
        select2: require('../vuejs/select2.vue')
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
        },
        phoneNumber: function() {
          if(this.phone.length) {
            return this.phone[0] === '0' ?
              this.phone.match(/[0-9]{1,2}/g).join(' ') :
              [this.phone[0]].concat(this.phone.slice(1).match(/[0-9]{1,2}/g)).join(' ');
          } else {
            return this.phone;
          }
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
    }

  }
});
