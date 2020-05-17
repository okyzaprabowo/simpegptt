window.Vue = require('vue');
window._ = {};
window._.forEach = require("lodash/forEach");
window.axios = require('axios');
window.moment = require('moment');

// Auto update layout
if (window.layoutHelpers) {
    window.layoutHelpers.setAutoUpdate(true);
}

$(function() {
    // Initialize sidenav
    $('#layout-sidenav').each(function() {
        new SideNav(this, {
            orientation: $(this).hasClass('sidenav-horizontal') ? 'horizontal' : 'vertical'
        });
    });

    // Initialize sidenav togglers
    $('body').on('click', '.layout-sidenav-toggle', function(e) {
        e.preventDefault();
        window.layoutHelpers.toggleCollapsed();
    });

    // Swap dropdown menus in RTL mode
    if ($('html').attr('dir') === 'rtl') {
        $('#layout-navbar .dropdown-menu').toggleClass('dropdown-menu-right');
    }
});

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */


window.axios.parseError = function (errResponse) {
    
    let err = { status: 400, message: "request error" , errors: []};
    //jika error server
    if (!errResponse.data) {
        err.message = errResponse.message;
    } else {
        err.message = errResponse.data.message;
        err.status = errResponse.data.status;

        if(errResponse.data.errors){
            err.errors = errResponse.data.errors;
            _.forEach(errResponse.data.errors,(v,i)=>{
                if(v!=true){
                    if(v instanceof Object){
                        _.forEach(v,(v2,i2)=>{
                            err.message += "<br> - " + v2;
                        });
                    }else{
                        err.message += "<br> - " + v;
                    }
                }
            });
        }
    }

    return err;
};
window.axios.errAlertText = {
    title: "Alert",
    text: "Session Expired"
}
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.get['Accepts'] = 'application/json';
window.axios.interceptors.response.use((response) => response, (error) => {
    if(error.response.data && error.response.data.message && error.response.data.errors && error.response.data.status) {
        let err = window.axios.parseError(error.response);
        //jika error token expired/auth gagal maka logoutkan
        if(err.status == 401){
            showAlert({
                type: "warning",
                title: window.axios.errAlertText.title,// Trans.get("alert.form_must_complete_title"),
                text: window.axios.errAlertText.text//Trans.get("alert.session_expired")
            });
            window.location.href = localUrl.logout;
        }else{
            err.errClass = error;
            throw err;
        }
    }else{
        throw error;
    }
});
/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true
// });
