<template>
    <div class="register-area2">
        <div class="register-text-Topic">
            <b>{{ lang.register_form }}</b>
        </div>
        <div class="form-group">
            <username-field v-bind:init-username="initUsername" ref="username_field" v-bind:direct-available="directAvailable" v-bind:direct-username-not-available="directUsernameNotAvailable" v-bind:facebook-not-pull-email="facebookNotPullEmail"></username-field>
            <select-makro-store-type
                    @select="selectMakroStoreType"
                    ref="selectMakroStoreType"
            ></select-makro-store-type>

            <makro-store-field
                    :makro-stores="makroStoresObj"
                    :show="showMakroStoreType === 'store'"
                    ref="makroStoreField"
            ></makro-store-field>

            <!--<makro-card-id-field ref="makro_card_id_field"></makro-card-id-field>-->

            <makro-card-id-field
                    ref="makro_card_id_field"
                    :show="showMakroStoreType === 'card'"
                    :show-makro-store-type="showMakroStoreType"
            ></makro-card-id-field>


            <accept-term-condition-field :terms-conditions="termsConditions" ref="accept_term_condition_field"></accept-term-condition-field>

            <div class="btn-box">
                <button class="btn-next" type="button" v-on:click="submitRegister" ref="submitRegisterBtn">{{ lang.next }} <loading v-bind:show="submitting"></loading></button>
            </div>
        </div>

    </div>
</template>

<script>
    import UsernameField from './field/Username'
    import MakroCardIdField from './field/MakroCardId'
    import AcceptTermConditionField from './field/AcceptTermCondition'
    import Loading from '../../loading/Loading'
    import popup from '../../../libraries/popup'
    import MakroStoreField from './field/MakroStore'
    import SelectMakroStoreType from './field/SelectMakroStoreType'

    export default {
        name: 'facebook-register',
        components: {
            SelectMakroStoreType,
            'username-field': UsernameField,
            'makro-card-id-field': MakroCardIdField,
            'accept-term-condition-field': AcceptTermConditionField,
            'loading': Loading,
            'makro-store-field': MakroStoreField
        },
        props: [
            'successUrl',
            'available',
            'directAvailable',
            'directUsernameNotAvailable',
            'facebookNotPullEmail',
            'returnUrl',
            'termsConditions',
            'makroStores'
        ],
        data: function () {
            return {
                username: this.$store.state.FacebookRegister.username,
                submitting: false,
                showMakroStoreType: 'card',
                makroStoresObj: []
            }
        },
        computed: {
            initUsername: function () {
                return this.$store.state.FacebookRegister.username
            },
            lang: function () {
                return this.$store.getters.lang;
            }
        },
        mounted: function () {
            if (this.$store.state.FacebookRegister.username != '') {
                this.$refs.username_field.checkUsername()
            }

            this.makroStoresObj = JSON.parse(this.makroStores)
        },
        methods: {
            submitRegister: function () {
                var me  = this;
                var passed = true;

                var checkUsernamePassed = this.$refs.username_field.checkUsername();
                passed = passed && checkUsernamePassed;

                var checkTermConditionPassed = this.$refs.accept_term_condition_field.checkTermCondition();
                passed = passed && checkTermConditionPassed;

                this.$refs.username_field.checkUsernameIsChecked();

                var usernameAvailablePassed = this.$refs.username_field.isUsernameVerified();
                passed = passed && usernameAvailablePassed;


                var makroStorePassed = true
                var makroCardIdPassed = true
                if (this.showMakroStoreType == 'store') {
                    makroStorePassed =  this.$refs.makroStoreField.validate();
                } else if (this.showMakroStoreType == 'card') {
                    makroCardIdPassed = this.$refs.makro_card_id_field.validateRequired()
                }

                passed = passed && makroStorePassed;
                passed = passed && makroCardIdPassed;


                var selectMakroStoreTypePassed = this.$refs.selectMakroStoreType.validate()
                passed = passed && selectMakroStoreTypePassed;

                if (passed === true) {
                    this.$refs.makro_card_id_field.recheckCardId(function() {
                        me.sendRegisterToApi();
                    });
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
                    //'makro_card_id': makroId,
                    'facebook_id': this.$store.state.FacebookRegister.facebook_user_id,
                    //'register_store_id': this.$refs.makroStoreField.makroStoreId
                };

                if (this.showMakroStoreType == 'store') {
                    param.register_store_id = this.$refs.makroStoreField.makroStoreId
                } else {
                    param.makro_card_id = makroId
                }

                var me = this;
                axios.post(this.$store.getters.locale_url + '/members/register/facebook', param)
                    .then(function (response) {
                        me.submitting = false;

                        if (response.data.status == 'ok') {
                            me.onRegisterSuccess();
                        } else {
                            me.onRegisterError(response);
                        }
                    })
                    .catch(function (response) {
                        me.onRegisterError(response);
                        me.submitting = false;
                    });
            },

            onRegisterSuccess: function()
            {
                window.location = this.successUrl;
            },

            onRegisterError: function (response) {
                var btn = $(this.$refs.submitRegisterBtn);
                btn.removeAttr('disabled');
                this.submitting = false;

                var message = this.lang.register_error_message;
                if (typeof response == 'object' && typeof response.data == 'object' &&  typeof response.data.errors == 'object' &&  response.data.errors ) {
                    if (typeof response.data.errors.facebook_id == 'object' || typeof response.data.errors.facebook_id == 'function') {
                        message = response.data.errors.facebook_id[0];
                    }
                }

                popup.open(this.lang.could_not_registered, message, 'error', true)

                if (typeof response == 'object' && typeof response.data == 'object' &&  typeof response.data.errors == 'object' &&  response.data.errors ) {
                    if (typeof response.data.errors.username == 'object' || typeof response.data.errors.username == 'array') {
                        this.$refs.username_field.show_username_error = true;
                        this.$refs.username_field.username_error_message = response.data.errors.username[0];
                        this.$refs.username_field.username_correct = false;
                    }
                }
            },

            selectMakroStoreType(type) {
                this.showMakroStoreType = type
            }
        }
    };

</script>