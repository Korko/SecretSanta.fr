import CryptoJS from 'crypto-js';

import Vue from 'vue';
import VueAjax from './ajaxVue.js';

window.app = new Vue({
  mixins: [VueAjax],

  el: '#form',

  data: {
    challenge: window.global.challenge,
    key: window.location.hash.substr(1),
    text: window.global.text,
    verified: false
  },

  created: function() {
      var challenge = JSON.parse(atob(this.challenge));
      var text = CryptoJS.AES.decrypt(challenge.value, CryptoJS.enc.Base64.parse(this.key), {
        iv : CryptoJS.enc.Base64.parse(challenge.iv)
      }).toString(CryptoJS.enc.Utf8);

      this.verified = (text === this.text);
  }
});

