<template>
    <div class="register-area">
        <div class="register-text-Topic">
            <b>{{ lang.register_form }}</b>
        </div>
        <div class="form-group">
            <username-field ref="username_field" v-bind:label="lang.username_label"></username-field>

            <label for="input_password">{{ lang.password }}<span style="color:#F01616;"> *</span></label>
            <div class="has-feedback" v-bind:class="passwordClass">
                <input type="password" class="form-control" aria-describedby="" v-bind:placeholder="lang.password" v-model.trim="password" v-on:blur="checkPassword" id="txt_password">
                <span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true" v-show="password_correct"></span>
               <div id="lbl_password">
                    <span class="remark" v-show="show_password_error">
                        <span v-show="!error_password_required" v-html="lang.password_requirement"></span>
                        <span v-show="error_password_required" v-html="lang.password_requirement"></span>
				    </span>
               </div>
            </div>

            <label for="input_confirm_password">{{ lang.confirm_password }}<span style="color:#F01616;"> *</span></label>
            <div class="has-feedback" v-bind:class="confirmPasswordClass">
                <input type="password" class="form-control" aria-describedby="" v-model.trim="confirm_password" v-on:blur="checkConfirmPassword" v-bind:placeholder="lang.confirm_password" id="txt_confirm_password">
                <span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true" v-show="confirm_password_correct"></span>
                <div id="lbl_confirm_password">
                    <span class="remark" v-show="show_confirm_password_error">
                        <span v-show="!error_confirm_password_required">
                            {{ lang.password_not_match }}
                        </span>
                         <span v-show="error_confirm_password_required">
                             {{ lang.password_not_match }}
                        </span>
                    </span>
                </div>
            </div>

            <select-makro-store-type
                @select="selectMakroStoreType"
                ref="selectMakroStoreType"
            ></select-makro-store-type>

            <makro-store-field
                :makro-stores="makroStores"
                :show="showMakroStoreType === 'store'"
                ref="makroStoreField"
            ></makro-store-field>

            <makro-card-id-field
                    ref="makro_card_id_field"
                    :show="showMakroStoreType === 'card'"
                    :show-makro-store-type="showMakroStoreType"
                    :show-label="false"
            ></makro-card-id-field>


            <accept-term-condition-field :terms-conditions="termsConditions" ref="accept_term_condition_field"></accept-term-condition-field>
            <br />
            <recaptcha-input ref="recaptcha"></recaptcha-input>


            <div class="btn-box">
                <button class="btn-next" type="button" ref="submitRegisterBtn" v-on:click="submitRegister" id="btn_register">{{ lang.register }} <loader :show="submitting"></loader></button>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'
    import reCaptcha from '../../input_fields/reCaptcha'
    import UsernameField from './field/Username'
    import MakroCardIdField from './field/MakroCardId'
    import AcceptTermConditionField from './field/AcceptTermCondition'
    import Loader from '../../loading/Loading'
    import popup from '../../../libraries/popup'
    import MakroStoreField from './field/MakroStore'
    import SelectMakroStoreType from './field/SelectMakroStoreType'

    export default {
        name: 'register',
        components: {
            SelectMakroStoreType,
            'username-field': UsernameField,
            'makro-card-id-field': MakroCardIdField,
            'accept-term-condition-field': AcceptTermConditionField,
            'recaptcha-input': reCaptcha,
            'loader': Loader,
            'makro-store-field': MakroStoreField
        },
        props: [
            'successUrl',
            'activateEmailWaitingUrl',
            'activateOtpUrl',
            'termsConditions',
            'makroStores'
        ],
        data: function () {
            return {
                username: '',
                makro_card_id: '',
                makro_card_id_correct: '',
                accept_term_condition: '',
                username_correct: false,
                show_username_error: false,
                username_error_message: '',
                username_type: '',
                number_of_username_blur: 0,
                show_password_error: false,
                show_confirm_password_error: false,
                password: '',
                confirm_password: '',
                password_correct: '',
                confirm_password_correct: '',
                error_password_required: false,
                error_confirm_password_required: false,
                showVerifyCardIdModal: false,
                valid_card_id: false,
                card_id_response: {
                    id: '',
                    customer_name: ''
                },
                verify_card_id_button_name: '',
                error_card_id_required: false,
                error_term_condition_required: false,
                check_card_id_button_name: '',
                error_card_id_not_verify: false,
                submitting: false,
                verifyCardIdCallback: null,
                showButton: window.showButton,
                showMakroStoreType: 'card' // 'store', 'card'
            }
        },
        methods: {
            checkUsername: function () {
                var valid = false;
                //Check username only it's not empty
                ++this.number_of_username_blur;

                if (this.username != '') {
                    var result = false;
                    var type = '';
                    //If user input number only
                    if (this.username.match(/^[0-9][0-9]+[0-9]$/i)) {
                        //Validate phone format
                        result = this.isValidPhone(this.username);
                        type = 'phone';
                    } else {
                        //Validate email format
                        result = this.isValidEmail(this.username);
                        type = 'email';
                    }

                    if (result === true) {
                        this.username_correct = true;
                        valid = true;
                    } else {
                        this.username_correct = false;

                        switch (type) {
                            case 'phone':
                                this.username_error_message = '*' + this.lang.invalid_phone_format;
                                break;
                            case 'email':
                                this.username_error_message = '*' + this.lang.invalid_email_format;
                                break;
                        }
                    }
                } else {
                    this.username_error_message = '*' + this.lang.required;
                    this.username_correct = false;
                    this.show_username_error = true;
                }

                return valid;
            },

            isValidPhone: function (value) {
                if (value.match(/^(06|08|09)[0-9]{8}$/i)) {
                    return true;
                }

                return false;
            },

            isValidEmail: function (value) {
                var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(value);
            },

            isValidPassword: function (value) {
                if (value.length < 8) {
                    return false;
                }
                var re = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/;
                return re.test(value);
            },


            isPasswordMatch: function (password, confirmPassword) {
                if (password === confirmPassword) {
                    return true;
                }

                return false;
            },

            checkUsernameFromApi: function () {
                axios.post(this.$store.getters.locale_url + '/members/check-username', {
                        'username': this.username,
                        'type': this.username_type
                    })
                    .then(function() {

                    })
                    .catch(function() {

                    });
            },

            checkPassword: function () {
                var valid = false;
                if (this.password != '') {
                    this.error_password_required = false;
                    if (!this.isValidPassword(this.password)) {
                        this.password_correct = false;
                        this.show_password_error = true;
                    } else {
                        this.password_correct = true;
                        this.show_password_error = false;
                        valid = true;
                    }

                    this.checkConfirmPassword(true);
                } else {
                    this.error_password_required = true;
                    this.password_correct = false;
                    this.show_password_error = true;
                }

                return valid;
            },

            checkConfirmPassword: function (notCheckRequired) {
                var valid = false;
                if (this.confirm_password != '') {
                    this.error_confirm_password_required = false;
                    if (!this.isPasswordMatch(this.password, this.confirm_password)) {
                        this.confirm_password_correct = false;
                        this.show_confirm_password_error = true;
                    } else {
                        this.confirm_password_correct = true;
                        this.show_confirm_password_error = false;
                        valid = true;
                    }
                } else {
                    if (notCheckRequired !== true) {
                        this.error_confirm_password_required = true;
                        this.confirm_password_correct = false;
                        this.show_confirm_password_error = true;
                    }
                }

                return valid;
            },

            checkCardId: function () {
                if (this.makro_card_id != '') {
                    this.makro_card_id_correct = true;
                    this.error_card_id_required = false;

                    this.verifyCardId(this.makro_card_id);

                } else {
                    this.makro_card_id_correct = false;
                    this.error_card_id_required = true;
                }
            },

            checkCardIdRequired: function () {
                var valid = false;
                if (this.makro_card_id != '') {
                    this.makro_card_id_correct = true;
                    this.error_card_id_required = false;
                    valid = true;
                } else {
                    this.makro_card_id_correct = false;
                    this.error_card_id_required = true;
                }

                return valid;
            },

            checkCardIdRequiredAndVerify: function () {
                var valid = this.checkCardIdRequired();
                if (valid) {
                    if (this.valid_card_id === true) {
                        this.error_card_id_not_verify = false;
                    } else {
                        this.error_card_id_not_verify = true;
                    }
                }
            },

            verifyCardId: function (value, callback) {
                var me = this;
                this.verifyCardIdCallback = callback;

                this.check_card_id_button_name = this.lang.please_wait;
                axios.get(this.$store.getters.locale_url + '/members/makro-card/' + value)
                    .then(function (response) {
                        if (response.data.status == 'ok') {
                            me.valid_card_id = true;
                            me.card_id_response.id = response.data.data.card_id;
                            me.card_id_response.customer_name = response.data.data.customer_name;
                            me.verify_card_id_button_name = me.lang.confirm;
                            me.error_card_id_not_verify = false;
                        } else {
                            me.valid_card_id = false;
                            me.verify_card_id_button_name = me.lang.ok;
                        }

                        me.showVerifyCardIdModal = true;
                        me.check_card_id_button_name = me.lang.verify;
                    })
                    .catch(function () {
                        me.valid_card_id = false;
                        me.verify_card_id_button_name = me.lang.ok;
                        me.showVerifyCardIdModal = true;
                        me.check_card_id_button_name = me.lang.verify;
                    });
            },

            onCloseVerifyCardIdModal: function () {
                this.showVerifyCardIdModal = false;
            },

            confirmVerifyCardId: function () {
                if (this.valid_card_id === true) {
                    if (typeof this.verifyCardIdCallback == 'function' || typeof this.verifyCardIdCallback == 'object') {
                        this.verifyCardIdCallback.apply(this, []);
                    }
                } else {
                    this.makro_card_id = '';
                }

                this.showVerifyCardIdModal = false;
            },

            checkTermCondition: function () {
                var valid = false;
                if (this.accept_term_condition === true) {
                    this.error_term_condition_required = false;
                    valid = true;
                }  else {
                    this.error_term_condition_required = true;
                }

                return valid;
            },

            submitRegister: function () {
                var me = this;
                var passed = true;

                var usernamePassed =  this.$refs.username_field.checkUsername();
                passed = passed && usernamePassed;
                console.log('usernamePassed', usernamePassed)

                this.$refs.username_field.checkUsernameIsChecked();

                var passwordPassed = this.checkPassword();
                passed = passed && passwordPassed;
                console.log('passwordPassed', passwordPassed)

                var confirmPasswordPassed = this.checkConfirmPassword();
                passed = passed && confirmPasswordPassed;
                console.log('confirmPasswordPassed', confirmPasswordPassed)

                var termConditionPassed = this.$refs.accept_term_condition_field.checkTermCondition();
                passed = passed && termConditionPassed;
                console.log('termConditionPassed', termConditionPassed)

                var usernameAvailablePassed = this.$refs.username_field.isUsernameVerified();
                passed = passed && usernameAvailablePassed;
                console.log('usernameAvailablePassed', usernameAvailablePassed)

                var recaptchaPassed =  this.$refs.recaptcha.validateReCaptcha();
                passed = passed && recaptchaPassed;
                console.log('recaptchaPassed', recaptchaPassed)

                var makroStorePassed = true
                var makroCardIdPassed = true
                if (this.showMakroStoreType == 'store') {
                    makroStorePassed =  this.$refs.makroStoreField.validate();
                } else if (this.showMakroStoreType == 'card') {
                    makroCardIdPassed = this.$refs.makro_card_id_field.validateRequired()
                }

                passed = passed && makroStorePassed;
                passed = passed && makroCardIdPassed;

                console.log('makroStorePassed', makroStorePassed)
                console.log('makroCardIdPassed', makroCardIdPassed)


                var selectMakroStoreTypePassed = this.$refs.selectMakroStoreType.validate()
                passed = passed && selectMakroStoreTypePassed;

                console.log('selectMakroStoreTypePassed', selectMakroStoreTypePassed)

                console.log('passed', passed)
                if (passed === true) {
                    this.$refs.makro_card_id_field.recheckCardId(function() {
                        me.sendRegisterToApi();
                    });
                } else {

                }
            },

            recheckCardId: function (callback) {
                var me = this;
                if (this.makro_card_id != '' && this.valid_card_id !== true) {
                    if (confirm(this.lang.confirm_verify_makro_card_id)) {
                        this.verifyCardId(this.makro_card_id, function () {
                            callback.apply(me, []);
                        });
                    } else {
                        this.makro_card_id = '';
                        callback.apply(me, []);
                    }
                } else {
                    callback.apply(this, []);
                }

            },

            sendRegisterToApi: function()
            {
                var btn = $(this.$refs.submitRegisterBtn);
                btn.attr('disabled', 'disabled');
                this.submitting = true;

                var makroId = this.$refs.makro_card_id_field.valid_card_id === true ? this.$refs.makro_card_id_field.makro_card_id : '';
                var param = {
                    'username': this.$refs.username_field.username,
                    'password': this.password,
                    'confirm_password': this.confirm_password,
                    //'makro_card_id': makroId,
                    'g-recaptcha-response': reCaptchaValid,
                    //'register_store_id': this.$refs.makroStoreField.makroStoreId
                };

                if (this.showMakroStoreType == 'store') {
                    param.register_store_id = this.$refs.makroStoreField.makroStoreId
                } else {
                    param.makro_card_id = makroId
                }

                var me = this;
                axios.post(this.$store.getters.locale_url + '/members/register', param)
                    .then(function (response) {
                        if (response.data.status == 'ok') {
                            me.onRegisterSuccess(response);
                        } else {
                            me.onRegisterError(response);
                        }
                    })
                    .catch(function (response) {
                        me.onRegisterError(response);
                    });
            },

            onRegisterSuccess: function(response)
            {
                if (response.data.data.attributes.is_activate) {
                    window.location = this.successUrl;
                } else {
                    if (response.data.data.attributes.activation.type == 'email') {
                        window.location = this.activateEmailWaitingUrl;
                    } else {
                        window.location = this.activateOtpUrl;
                    }
                }
            },

            onRegisterError: function (response) {
                var btn = $(this.$refs.submitRegisterBtn);
                btn.removeAttr('disabled');
                this.submitting = false;

                var shownError = false
                if (typeof response.data == 'object'
                    && typeof response.data.errors == 'object') {
                    var recaptchaError = false
                    for (var i in response.data.errors) {
                        if (i == 'g-recaptcha-response') {
                            recaptchaError = true
                            break
                        }
                    }

                    //On recaptcha error
                    if (recaptchaError) {
                        shownError = true
                        grecaptcha.reset()
                        reCaptchaValid = false
                        popup.open({
                            type: 'error',
                            title: this.lang.could_not_registered,
                            message: this.lang.recpatcha_exipre_or_invalid_please_validate_recaptcha_again
                        })
                    }
                }

                if (!shownError) {
                    var message = message = this.lang.register_error_message
                    if (typeof response.data == 'object'
                        && typeof response.data.message == 'string')
                    {
                        message = response.data.message
                    }
                    popup.open(this.lang.could_not_registered, message, 'error', true)
                }

                if (typeof response == 'object' && typeof response.data == 'object' && typeof response.data.data == 'object') {
                    if (typeof response.data.data.error == 'object') {
                        if (typeof response.data.data.error.username == 'string') {
                            this.show_username_error = true;
                            this.username_error_message = response.data.data.error.username;
                            this.username_correct = false;
                        }
                    }
                }
            },

            selectMakroStoreType(type) {
                this.showMakroStoreType = type
            }
        },

        watch: {
            accept_term_condition: function (value) {
                if (value === true) {
                    this.error_term_condition_required = false;
                } else {
                    this.error_term_condition_required = true;
                }
            },

            makro_card_id: function (newValue, oldValue) {
                this.valid_card_id = false;
            }
        },

        computed: {
            usernameClass: function () {
                var hasSuccess = false;
                var hasFeedback = false;
                var hasError = false;
                if (this.number_of_username_blur > 0) {
                    if (this.username_correct) {
                        hasError = false;
                        hasSuccess = true;
                        hasFeedback = true;
                        this.show_username_error = false;
                    } else {
                        hasError = true;
                        hasSuccess = false;
                        hasFeedback = false;
                        this.show_username_error = true;
                    }
                }
                return {
                    'has-success': hasSuccess,
                    'has-feedback': hasFeedback,
                    'has-error': hasError
                }
            },

            passwordClass: function () {
                var hasError = false;
                var hasSuccess = false;
                if (this.password_correct === false ) {
                    hasError = true;
                }

                if (this.password_correct === true ) {
                    hasSuccess = true;
                }

                return {
                    'has-error': hasError,
                    'has-success': hasSuccess
                }
            },

            confirmPasswordClass: function () {
                var hasError = false;
                var hasSuccess = false;
                if (this.confirm_password_correct === false ) {
                    hasError = true;
                }

                if (this.confirm_password_correct === true ) {
                    hasSuccess = true;
                }

                return {
                    'has-error': hasError,
                    'has-success': hasSuccess
                }
            },

            makroIdClass: function () {
                return {
                    'has-error': this.makro_card_id_correct === false ? true : false,
                    'has-success': this.valid_card_id === true ? true : false
                };
            },
            lang: function () {
               return this.$store.getters.lang;
            }
        },

        mounted: function () {
            this.verify_card_id_button_name = this.lang.confirm;
            this.check_card_id_button_name = this.lang.verify;
        }
    };
</script>