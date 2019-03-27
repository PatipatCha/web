<template>
    <div class="register-area">
        <div class="register-text-Topic">
            <b>{{ lang.register_with_facebook }}</b>
        </div>

        <div class="alert alert-danger" role="alert" v-show="isError" v-html="errorMessage">

        </div>

        <div class="btn-box2">
            <a v-bind:href="facebookLoginUrl" id="btn_login_with_fb"><button class="btn-login-with-fb" type="">
                <img v-bind:src="facebookIcon" width="20">&nbsp;&nbsp;&nbsp;{{ lang.login_with_facebook }}
            </button></a>
        </div>

        <div class="box-text">
            {{ lang.create_username_and_password_with_facebook_connect }}
        </div>

    </div>
</template>

<script>
    export default {
        name: 'facebook-login',
        props: [
            'facebookLoginUrl'
        ],
        data: function () {
            return {
                facebookIcon: this.$store.getters.url + '/assets/images/icon-fb.png'
            }
        },
        computed: {
            isError: function () {
                var error = this.$store.state.FacebookLogin.error;
                if (error != '') {
                    return true;
                }

                return false;
            },
            errorMessage: function () {
                return this.$store.state.FacebookLogin.error;
            },
            lang: function () {
                return this.$store.getters.lang;
            }
        },
    };
</script>