<template>
    <div class="form-group pay-pickup" >

            <div class="col-lg-12 col-sm-12 no-padding">
                <label :for="getShopNameId">{{ lang.shop_name }}</label>
                <input type="text" class="form-control" v-bind:name="getInputName('shop_name')" :data-rule-no-special-char="true" :placeholder="lang.shop_name" v-model="shop_name" data-rule-maxlength="100" :data-msg-maxlength="lang.validate_maxlength_shop_name" :id="getShopNameId">
            </div>
            <!---->
            <div class="col-lg-6 col-md-6 col-sm-6 padding-right-ad">
                <label :for="getFirstNameId">{{  lang.name_label }}<span style="color:#F01616;" v-show="firstNameRequired"> *</span></label>
                <input type="text" class="form-control" v-bind:name="getInputName('first_name')" :data-rule-no-special-char="true" :placeholder="lang.name_label" v-model="first_name" data-rule-maxlength="64" v-bind:data-rule-required="firstNameRequired" v-bind:required="firstNameRequired" :id="getFirstNameId" :data-msg-required="lang.please_enter_first_name">
                <label class="error" :for="firstNameId"  :id="labelFirstName"></label>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6 padding-left-ad">
                <label :for="getLastNameId">{{ lang.surname_label }}<span style="color:#F01616;" v-show="lastNameRequired"> *</span></label>
                <input type="text" class="form-control" v-bind:name="getInputName('last_name')" :data-rule-no-special-char="true" :placeholder="lang.surname_label" v-model="last_name" data-rule-maxlength="64" v-bind:data-rule-required="lastNameRequired" v-bind:required="lastNameRequired" :id="getLastNameId" :data-msg-required="lang.please_enter_last_name">
                <label class="error" :for="lastNameId"  :id="labelLastName"></label>
            </div>
            <!---->
            <div class="col-lg-6 col-md-6 col-sm-6 padding-right-ad">
                <label :for="getTelephoneNumberId">{{ lang.telephone_number_label }}<span style="color:#F01616;" v-show="phoneRequired"> *</span></label>
                <input type="text" class="form-control" v-bind:name="getInputName('phone')" :data-rule-no-special-char="true" :placeholder="lang.telephone_number_label_placeholder" v-model="phone" data-rule-minlength="10" data-rule-maxlength="10" v-bind:data-rule-required="phoneRequired" v-bind:required="phoneRequired" data-rule-digits="true" :data-msg-maxlength="lang.please_enter_mobile_number" :data-msg-minlength="lang.please_enter_mobile_number" :id="getTelephoneNumberId" :data-msg-required="lang.please_enter_mobile_number">
                <label class="error" :for="telephoneNumberId"  :id="labelMobileNumber"></label>
            </div>
            <!---->
            <div class="col-lg-6 col-md-6 col-sm-6 padding-left-ad">
                <label :for="getEmailId">{{ lang.e_mail_label }}<span v-if="emailRequired" style="color:#F01616;"> *</span></label>
                <input type="text" class="form-control email-field" v-bind:name="getInputName('email')" :placeholder="lang.email_placholder" v-model="email" data-rule-maxlength="150" :data-rule-required="emailRequired" :data-rule-email="true" :id="getEmailId" :data-msg-email="lang.please_enter_valid_email_format">
                <label class="error" :for="emailId"  :id="labelEmail"></label>
            </div>
            <residence-address
                    v-bind:input-name="inputName"
                    v-bind:init-data="initData"
                    v-bind:ignore-validate-class="ignoreValidateClass"
                    :use-delivery-residence-address="useDeliveryResidenceAddress"
                    :address-id="addressNameId"
                    :province-id="provinceId"
                    :district-id="districtId"
                    :sub-district-id="subDistrictId"
                    :label-address="labelAddress"
                    :label-province="labelProvince"
                    :label-district="labelDistrict"
                    :label-sub-district="labelSubDistrict"
                    :label-postcode="labelPostcode"
                    :postcode-id-name="postcodeIdName"
            >
            </residence-address>
            <div class="clearfix"></div>
    </div>
</template>

<script>
    import ResidenceAddress from '../address/Residence'
    import 'jquery-validation'

    export default {
        name: 'cart-checkout-address',
        components: {
            ResidenceAddress
        },
        props: {
            inputName: {
                type: String,
                default: ''
            },
            initData: {
                type: Object
            },

            emailRequired: {
                type: Boolean,
                default: true
            },
            memberProfile: {
                type: Object
            },
            ignoreValidateClass: {
                type: String
            },
            firstNameRequired: {
                type: Boolean,
                default: true
            },
            lastNameRequired: {
                type: Boolean,
                default: true
            },
            phoneRequired: {
                type: Boolean,
                default: true
            },
            useDeliveryResidenceAddress: {
                type: Boolean,
                default: false
            },
            shopNameId: {
                type: String,
                default: ''
            },
            firstNameId: {
                type: String,
                default: ''
            },
            lastNameId: {
                type: String,
                default: ''
            },
            telephoneNumberId: {
                type: String,
                default: ''
            },
            emailId: {
                type: String,
                default: ''
            },
            addressNameId: {
                type: String,
                default: ''
            },
            provinceId: {
                type: String,
                default: ''
            },
            districtId: {
                type: String,
                default: ''
            },
            subDistrictId: {
                type: String,
                default: ''
            },
            btnSaveId: {
                type: String,
                default: ''
            },
            btnCancelId: {
                type: String,
                default: ''
            },
            labelFirstName: {
                type: String,
                default: ''
            },
            labelLastName: {
                type: String,
                default: ''
            },
            labelMobileNumber: {
                type: String,
                default: ''
            },
            labelEmail: {
                type: String,
                default: ''
            },
            labelAddress: {
                type: String,
                default: ''
            },
            labelProvince: {
                type: String,
                default: ''
            },
            labelDistrict: {
                type: String,
                default: ''
            },
            labelSubDistrict: {
                type: String,
                default: ''
            },
            labelPostcode: {
                type: String,
                default: ''
            },
            postcodeIdName: {
                type: String,
                default: ''
            }
        },
        data: function ()
        {
            return {
                shop_name: this.getShopName(),
                first_name: this.getFirstName(),
                last_name: this.getLastName(),
                phone: this.getPhone(),
                email: this.getEmail(),
                addressId: ''
            };
        },
        methods: {
            getInputName: function (name) {
                if (this.inputName.length > 0) {
                    return this.inputName + '[' + name + ']';
                }

                return name;
            },
            getShopName: function () {
                if (typeof this.initData == 'object' && this.initData) {
                    if (typeof this.initData.address_name == 'string' || typeof this.initData.shop_name == 'string' ) {
                        if (typeof this.initData.address_name == 'string') {
                            return this.initData.address_name;
                        }

                        return this.initData.shop_name
                    }
                }


                return '';
            },
            getFirstName: function () {
//                if (typeof this.memberProfile == 'object' && this.memberProfile) {
//                    if (typeof this.memberProfile.first_name == 'string' ) {
//                        return this.memberProfile.first_name
//                    }
//                }
                if (typeof this.initData == 'object' && this.initData) {
                    if (typeof this.initData.first_name == 'string' ) {
                        return this.initData.first_name
                    }
                }

                return '';
            },
            getLastName: function () {
//                if (typeof this.memberProfile == 'object' && this.memberProfile) {
//                    if (typeof this.memberProfile.last_name == 'string' ) {
//                        return this.memberProfile.last_name
//                    }
//                }
                if (typeof this.initData == 'object' && this.initData) {
                    if (typeof this.initData.last_name == 'string' ) {
                        return this.initData.last_name
                    }
                }


                return '';
            },
            getPhone: function () {
                if (typeof this.initData == 'object' && this.initData) {
                    if (typeof this.initData.contact_phone == 'string' || typeof this.initData.phone == 'string' ) {
                        if (typeof this.initData.contact_phone == 'string') {
                            return this.initData.contact_phone;
                        }

                        return this.initData.phone
                    }

                }

                return '';
            },
            getEmail: function () {
                if (typeof this.initData == 'object' && this.initData) {
                    if (typeof this.initData.contact_email == 'string' || typeof this.initData.email == 'string' ) {
                        if (typeof this.initData.contact_email == 'string') {
                            return this.initData.contact_email;
                        }

                        return this.initData.email
                    }
                }

                return '';
            }
        },
        mounted: function () {
            $(this.$refs.address_form).validate();
        },
        watch: {
            initData: function () {
                if (this.initData) {
                    this.shop_name =  this.getShopName();
                    this.first_name = this.getFirstName();
                    this.last_name = this.getLastName();
                    this.phone = this.getPhone();
                    this.email = this.getEmail();
                } else {
                    this.shop_name =  '';
                    this.first_name = '';
                    this.last_name = '';
                    this.phone = '';
                    this.email = '';
                }
            }
        },
        computed: {
            lang: function () {
                return this.$store.getters.lang;
            },
            getShopNameId() {
                if (_.isEmpty(this.shopNameId)) {
                    return false
                }

                return this.shopNameId
            },
            getFirstNameId() {
                if (_.isEmpty(this.firstNameId)) {
                    return false
                }

                return this.firstNameId
            },
            getLastNameId() {
                if (_.isEmpty(this.lastNameId)) {
                    return false
                }

                return this.lastNameId
            },
            getTelephoneNumberId() {
                if (_.isEmpty(this.telephoneNumberId)) {
                    return false
                }

                return this.telephoneNumberId
            },
            getEmailId() {
                if (_.isEmpty(this.emailId)) {
                    return false
                }

                return this.emailId
            }
        }
    }
</script>