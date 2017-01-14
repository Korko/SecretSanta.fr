var CryptoJS = require('crypto-js');

var VueAjax = require('./ajaxVue.js');
window.app = new VueAjax({
  el: '#form',

  data: {
    challenge: global.challenge,
    iv: global.iv,
    key: window.location.hash.substr(1),
    text: global.text,
    verified: false
  },

  created: function() {
      var iv = CryptoJS.enc.Hex.parse(this.iv);
      var key = CryptoJS.enc.Hex.parse(this.key);

      var text = CryptoJS.AES.decrypt(this.challenge, key, {iv: iv}).toString(CryptoJS.enc.Utf8);
      this.verified = (text === this.text);
  }
});

