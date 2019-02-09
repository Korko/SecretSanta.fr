import Vue from 'vue';
import VueAjax from './ajaxVue.js';
import VueDecrypt from './decrypterVue.js';

window.app = new Vue({
  mixins: [VueAjax, VueDecrypt],

  el: '#form',

  data: {
    challenge: window.global.challenge,
    key: window.location.hash.substr(1),
    text: window.global.text,
    verified: false
  },

  created: function() {
      this.verified = (this.decrypt(this.challenge) === this.text);
  }
});

