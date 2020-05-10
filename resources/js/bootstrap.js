
/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */
window.Vue = require('vue');
try {
    //window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });
window.moment = require('moment');

import Echo from 'laravel-echo'

import VueEcho from 'vue-echo'

window.Pusher = require('pusher-js');

const EchoInstance = new Echo({
    broadcaster: 'pusher',
    key: '0c0c39d4df6894b50366',
    cluster: 'ap1'
});

Vue.use(VueEcho, EchoInstance);

/**
 *  Init Lang for translations

let translations=require('./extra/messages.js');
let Lang = require('lang.js');
const lang = new Lang({
    messages: translations,
    locale: window.lang,
    fallback: window.lang
});
Vue.filter('trans', (...args) => {
    return lang.get(...args);
});
 */

/**
 *  Init SweetAlert
 */
import VueSweetAlert2 from 'vue-sweetalert2';

Vue.use(VueSweetAlert2);
