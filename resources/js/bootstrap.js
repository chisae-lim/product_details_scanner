/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

import jquery from 'jquery'
window.$ = window.jQuery = jquery;

import moment from 'moment';
window.moment = moment;

import Swal from 'sweetalert2'
window.$swal = Swal;

import { LoadingModal, MessageModal, CloseModal, ErrorModal } from './modules/swal.js';

window.LoadingModal = LoadingModal;
window.MessageModal = MessageModal;
window.CloseModal = CloseModal;
window.ErrorModal = ErrorModal;


window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';


window.PDF_FONTS = {
    Aparajita: {
        normal: window.origin + '/assets/fonts/aparajita/aparaj.ttf',
        bold: window.origin + '/assets/fonts/aparajita/aparajb.ttf',
        italics: window.origin + '/assets/fonts/aparajita/aparaji.ttf',
        bolditalics: window.origin + '/assets/fonts/aparajita/aparajbi.ttf'
    },
    Roboto: {
        normal: window.origin + '/assets/fonts/roboto/Roboto-Regular.ttf',
        bold: window.origin + '/assets/fonts/roboto/Roboto-Bold.ttf',
        italics: window.origin + '/assets/fonts/roboto/Roboto-Italic.ttf',
        bolditalics: window.origin + '/assets/fonts/roboto/Roboto-BoldItalic.ttf'
    },
    KhmerOSMoul: {
        normal: window.origin + '/assets/fonts/Khmer OS Moul Regular.ttf',
    },
    KhmerOldStyle: {
        normal: window.origin + '/assets/fonts/Khmer Old Style Regular.ttf',
    },
    KhmerOSFreehand: {
        normal: window.origin + '/assets/fonts/Khmer OS Freehand Regular.ttf',
    },
    KhmerOSContent: {
        normal: window.origin + '/assets/fonts/Khmer OS Content Regular.ttf',
    },
    KhmerOSSystem: {
        normal: window.origin + '/assets/fonts/Khmer OS System Regular.ttf',
    },
}
window.LOGOS = {
    bigLogo: window.origin + '/assets/logos/aet.png',
}
window.DATE_FORMAT = 'DD-MM-YYYY';
/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
//     wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });
