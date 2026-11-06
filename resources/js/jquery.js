// Exposes jQuery globally before plugins (jquery.actual) that expect it.
import jQuery from 'jquery';
window.$ = window.jQuery = jQuery;
export default jQuery;
