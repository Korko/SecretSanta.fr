var alertify = require('alertify.js');
if(window.global.alert) {
    alertify.alert(window.global.alert);
}

window.submitForm = function() {
    jQuery('#randomForm .submitform').click();
};
