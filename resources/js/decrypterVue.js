import CryptoJS from 'crypto-js';

export default {
  data: {
    key: window.location.hash.substr(1)
  },

  methods: {
    decrypt: function(data) {
      var datab = JSON.parse(atob(data));
      return CryptoJS.AES.decrypt(datab.value, CryptoJS.enc.Base64.parse(this.key), {
        iv : CryptoJS.enc.Base64.parse(datab.iv)
      }).toString(CryptoJS.enc.Utf8);
    }
  }
}
