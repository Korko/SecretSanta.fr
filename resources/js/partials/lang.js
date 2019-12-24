import Lang from 'lang.js';
import messages from './messages.js';

var LangObj = new Lang({
    messages
});
LangObj.setLocale('fr');

export default LangObj;
