<template>

            <div>
                <form method="post" ref="form" v-bind:action="action">
                <div class="forget-pass-text-Topic">
                    <b>{{ lang.forget_password }}</b>
                </div>
                <div class="form-group">
                    <username-field ref="username_field"
                                    v-bind:label="lang.username"
                                    v-bind:show-check-username="false"
                                    v-bind:empty-box-class="true"
                                    v-bind:place-holder="lang.forgot_password_username_placeholder"
                                    v-bind:need-verify-username="false"
                                    v-bind:init-username="oldUsername">
                    </username-field>
                    <div class="captcha-box">
                        <recaptcha ref="recaptcha"></recaptcha>
                    </div>

                    <div class="btn-box">
                        <button class="btn-ok" type="button" v-on:click="submit">{{ lang.ok }}</button>
                    </div>
                </div>

                <input type="hidden" name="_token" v-bind:value="csrf">
                </form>
            </div>
</template>

<script>
    import Recaptcha from '../../input_fields/reCaptcha'
    import UsernameField from '../register/field/Username'
    export default {
        name: 'forgot-password',
        components: {
            'recaptcha': Recaptcha,
            'username-field': UsernameField
        },
        props: [
            'csrf',
            'oldUsername'
        ],
        data: function () {
            return {
                error: true,
                errorMessage: 'test',
                action: this.$store.getters.locale_url + '/members/forget-password/check-username'
            }
        },
        methods: {
            submit: function () {
                if (this.validate()) {
                    $(this.$refs.form).trigger('submit');
                }
            },

            validate: function () {
                var passed = true;

                var usernamePassed =  this.$refs.username_field.checkUsername();
                passed = passed && usernamePassed;

                var recaptchaPassed =  this.$refs.recaptcha.validateReCaptcha();
                passed = passed && recaptchaPassed;

                return passed;

            }
        },
        computed: {
            lang: function () {
                return this.$store.getters.lang;
            }
        }
    }
</script>