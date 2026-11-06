import alertify from 'alertifyjs';

alertify.defaults.transition = "slide";
alertify.defaults.theme.ok = "btn btn-primary";
alertify.defaults.theme.cancel = "btn btn-danger";
alertify.defaults.theme.input = "form-control";

alertify.defaults.notifier.position = 'top-right'

const lang = document.documentElement.lang.substr(0, 2);
import Locale from '@lang';

// Extend existing 'alert' dialog
if (!alertify.errorAlert) {
    //define a new errorAlert base on alert
    alertify.dialog('errorAlert', function() {
        return {
            setup() {
                return {
                    options: {
                        frameless: false,
                        movable: false,
                        closableByDimmer: false,
                        maximizable: false,
                        resizable: false
                    },
                    // Buttons and Focus copied from the source for alert dialog
                    buttons: [
                        {
                            text: alertify.defaults.glossary.ok,
                            key: 27, // Escape
                            invokeOnClose: true,
                            className: alertify.defaults.theme.ok,
                        }
                    ],
                    focus: {
                        element: 0,
                        select: false
                    }
                };
            },
            build() {
                var errorHeader = '<span class="fa fa-times-circle fa-2x" '
                +    'style="vertical-align:middle;color:#e10000;">'
                + '</span> ' + Locale[lang].common.internal;
                this.setHeader(errorHeader);
            }
        };
    }, true, 'alert');
}

// Extend existing 'alert' dialog
if (!alertify.confirmAlert) {
    //define a new confirmAlert base on alert
    alertify.dialog('confirmAlert', function() {
        return {
            setup() {
                return {
                    options: {
                        frameless: false,
                        movable: false,
                        closableByDimmer: false,
                        maximizable: false,
                        resizable: false
                    },
                    // Buttons and Focus copied from the source for alert dialog
                    buttons: [
                        {
                            text: alertify.defaults.glossary.ok,
                            key: 27, // Escape
                            invokeOnClose: true,
                            className: alertify.defaults.theme.ok,
                        }
                    ],
                    focus: {
                        element: 0,
                        select: false
                    }
                };
            },
            build() {
                var successHeader = '<span class="fa fa-check fa-2x" '
                +    'style="vertical-align:middle;color:#00e100;">'
                + '</span> ' + Locale[lang].common.success;
                this.setHeader(successHeader);
            }
        };
    }, true, 'alert');
}


export default alertify;
// Confirmation where the user must type `word` (replaces vuejs-dialog "hard" confirm)
export const hardConfirm = ({ title, body, word, help, ok, cancel }) => new Promise(resolve => {
    alertify.prompt(title, body + '<div class="mt-2">' + help.replace('[+:verification]', word) + '</div>', '', (event, value) => {
        if (value.trim() === word) {
            resolve();
        } else {
            event.cancel = true;
        }
    }, () => {}).set('labels', { ok, cancel });
});
