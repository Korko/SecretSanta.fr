import jQuery from 'jquery';

import alertify from 'alertify.js';
import SmsTools from './smsTools.js';

import Vue from 'vue';
import VueAutosize from 'vue-autosize';
Vue.use(VueAutosize);

import Modernizr from './modernizr.js';
import Moment from 'moment';
import Papa from 'papaparse';

import Lang from './lang.js';
Lang.setLocale(window.global.lang);

import Select2 from './components/select2.vue';

import VueAjax from './ajaxVue.js';
window.app = new VueAjax({
  el: '#form',

  data: {
    participants: [],
    smsContent: '',
    maxSms: global.maxSms,
    dearsanta: false,
    date: window.now,
    showModal: false,
    importing: false
  },

  components: {
    csv: {
      template: '#csv-template',
      components: {
        modal: {
          template: '#modal-template'
        }
      },
      methods: {
        emitSubmit: function() {
          this.$emit('import', $('#uploadCsv input[type=file]')[0].files[0]);
          this.$emit('close');
        },
        emitCancel: function() {
          this.$emit('close');
        }
      }
    },
    participant: {
      template: '#participant-template',
      props: {
          idx: {
              type: Number,
              required: true
          },
          name: {
              type: String,
              default: ''
          },
          email: {
              type: String,
              default: ''
          },
          phone: {
              type: String,
              default: ''
          },
          participants: {
              type: Array,
              required: true
          },
          dearsanta: {
              type: Boolean,
              default: false
          },
          smsdisabled: {
              type: Boolean,
              default: false
          }
      },
      data: function() {
        return {
          exclusions: []
        };
      },
      components: {
        select2: Select2
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
        used = used || !!this.participants[i].email;
      }
      return used;
    },

    phoneUsed: function() {
      var used = false;
      for(var i in this.participants) {
        used = used || !!this.participants[i].phone;
      }
      return used;
    },

    allMails: function() {
      var allMails = true;
      this.participants.forEach(function(participant) {
        allMails = (allMails && (participant.name === '' || participant.email !== ''));
      });
      return allMails;
    },

    longuestName: function() {
      var name = '';
      this.participants.forEach(function(participant) {
        name = (participant.name && SmsTools.length(participant.name) > SmsTools.length(name)) ? participant.name : name;
      });
      return name;
    },

    maxSmsContent: function() {
      return this.smsContent.replace('{SANTA}', this.longuestName)
                            .replace('{TARGET}', this.longuestName);
    },

    smsCount: function() {
      return Math.min(SmsTools.chunk(this.maxSmsContent).length, this.maxSms);
    },

    charactersLeft: function() {
      return SmsTools.chunkMaxLength(this.maxSmsContent, this.smsCount, true) - this.maxSmsContent.length;
    },

    maxLength: function() {
      return SmsTools.chunkMaxLength(this.maxSmsContent, this.maxSms, true);
    }

  },

  created: function() {
    this.addParticipant();
    this.addParticipant();
    this.addParticipant();

    Vue.nextTick(function() {
      if (!Modernizr.inputtypes.date) {
        $('input[type=date]', this.$el).datepicker({
          // Consistent format with the HTML5 picker
          dateFormat: 'yy-mm-dd',
          minDate: Moment(this.now).add(1, 'day').toDate(),
          maxDate: Moment(this.now).add(1, 'year').toDate()
        });
      }

      if (!Modernizr.filereader) {
        $('.participants-imports').remove();
      }
    }.bind(this));
  },

  filters: {
    moment: function (date, amount, unit) {
      return Moment(date).add(amount, unit).format("YYYY-MM-DD");
    }
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
    },

    allMails: function(newVal) {
      this.dearsanta = this.dearsanta && newVal;
    }
  },

  methods: {

    resetParticipants: function() {
      this.participants = [];
    },

    addParticipant: function(name, email, phone) {
      this.participants.push({
        name: name,
        email: email,
        phone: phone,
        id: 'id' + this.participants.length + (new Date()).getTime()
      });
    },

    importParticipants: function(file) {
      this.importing = true;
      var test = Papa.parse(file, {
        error: function() {
          this.importing = false;
          alertify.alert(Lang.get('csv.importError'));
        },
        complete: function(file) {
          this.importing = false;
          this.resetParticipants();
          file.data.forEach(function(participant) {
            if(participant[0] !== '') {
                this.addParticipant(participant[0], participant[1], participant[2]);
            }
          }.bind(this));
          if(this.participants.length < 3) {
            for(var i = 0; i < 3 - this.participants.length; i++) {
              this.addParticipant();
            }
          }
          alertify.alert(Lang.get('csv.importSuccess'));
        }.bind(this)
      });
    }

  }
});
