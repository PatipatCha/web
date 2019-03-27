import store from '../store'
import { SET_LOGGED_IN } from '../store/mutation-types'

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        "X-Requested-With": "XMLHttpRequest"
    }
})

$(document).on('submit', '#form-login-modal', function(e){
    e.preventDefault()
    
    $('.special-char-error', $('#form-login-modal')).hide()

   // console.log(store.state.appModule.login_callback);
    var username = $('#input-username').val()
    var password = $('#input-password').val()
    var redirect_url = $('#input_redirect_url').val();
    var remember_me = $('#remember_me').prop('checked');

    if (remember_me) {
        remember_me = 1;
    } else {
        remember_me = 0;
    }

    $('.btn-login').attr('disabled', 'disabled')
    $('#login-alert').addClass('hide')

    if (!username || !password) {
        $('.btn-login').removeAttr('disabled')
        $('#login-alert').html(store.getters.lang.username_and_password_are_required).removeClass('hide')
    } else {
        $.ajax({
            dataType : 'json',
            type     : 'POST',
            url      : store.getters.locale_url + '/members/login',
            data     : {
                username : username,
                password : password,
                redirect_url: redirect_url,
                remember_me: remember_me
            },
            beforeSend: function () {
                $('.login-loader').removeClass('hide');
            },
            error: function () {
                $('.login-loader').addClass('hide');
                $('.btn-login').removeAttr('disabled')
            }
        }).done(function(response){

            $('.login-loader').addClass('hide');

            if (response.status == 'success') {
                var member = response.data

                $('#login-modal').modal('hide')

                $('#user-menu-desktop').html(response.user_menu)
                $('#user-menu-mobile').html(response.user_menu_mobile)

                // Set Cookie/Session
                //
                // (have done it in memberauthcontroller ... )
                //

                // Display Login Data
                //
                //

                store.commit(SET_LOGGED_IN, 1)

                if(store.state.appModule.login_callback){
                    store.state.appModule.login_callback();
                }else{
                    if (response.redirect_url != '') {
                        window.location = response.redirect_url;
                    } else {
                        window.location.reload();
                    }
                }
            } else {
                $('.btn-login').removeAttr('disabled')
                $('#login-alert').html(response.message).removeClass('hide')
            }
        })
    }
})


$(function () {
    //Show login popup if is required from another page
    if (typeof GLOBAL_SETTING.required_login == 'number') {
        if (GLOBAL_SETTING.required_login === 1) {
            $('#login-modal').modal('show');
        }
    }
});