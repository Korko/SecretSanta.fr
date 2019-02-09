import Vue from 'vue';
import VueAjax from './ajaxVue.js';
import VueDecrypt from './decrypterVue.js';

window.app = new Vue({
  mixins: [VueAjax, VueDecrypt],

  el: '#form',

  data: {
    challenge: window.global.challenge,
    text: window.global.text,
    verified: false,
    raw_participants: window.global.participants,
    participants: []
  },

  created: function() {
    this.verified = (this.decrypt(this.challenge) === this.text);

    if (this.verified) {
      this.raw_participants.forEach((participant) => {
        this.participants.push({
          name: this.decrypt(participant.name),
          email_address: this.decrypt(participant.email_address),
          delivery_status: participant.delivery_status
        });
      });
    }
  }

});

