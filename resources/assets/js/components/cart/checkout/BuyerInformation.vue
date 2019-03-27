<template>
    <div>
        <div class="pay-pickup-text text-bold">
            <b>{{ lang.buyer_information }}</b>
        </div>

        <div class="message-box" v-if="showInformationForm">
            <p><strong class="text-bold">{{ address.address_name }}</strong></p>
            <p v-html="address.first_name + ' ' + address.last_name"></p>
            <p><b class="text-bold">{{ lang.address }} : </b> <full-address v-bind:address="address"></full-address></p>
            <p><b class="text-bold">{{ lang.tel }} : </b> {{ address.contact_phone }}</p>
            <p><b class="text-bold">{{ lang.e_mail }} : </b> {{ address.contact_email }}</p>
            <div class="action">
                <button type="button" class="btn btn-default btn-xs" v-on:click="editAddress" id="btn_edit_customer_information">{{ lang.edit }}</button>
            </div>

            <input type="hidden" name="use_existing_buyer_information" value="1">
        </div>

        <!-- ก้อน Buyer Information (เฉพาะ form ที่อยู่) -->
        <cart-checkout-address
                v-bind:init-data="addressData"
                input-name="customer"
                v-if="!showInformationForm"
                v-bind:email-required="false"
                ignore-validate-class="first-do-not-ignore"
                :shop-name-id="shopNameId"
                :first-name-id="firstNameId"
                :last-name-id="lastNameId"
                :telephone-number-id="telephoneNumberId"
                :email-id="emailId"
                :address-name-id="addressId"
                :province-id="provinceId"
                :district-id="districtId"
                :sub-district-id="subDistrictId"
                :btn-save-id="btnSaveId"
                :btn-cancel-id="btnCancelId"
                :label-first-name="labelFirstName"
                :label-last-name="labelLastName"
                :label-mobile-number="labelMobileNumber"
                :label-email="labelEmail"
                :label-address="labelAddress"
                :label-province="labelProvince"
                :label-district="labelDistrict"
                :label-sub-district="labelSubDistrict"
                :label-postcode="labelPostcode"
                :postcode-id-name="postcodeIdName"
        >
        </cart-checkout-address>
        <!-- ก้อน Buyer Information (เฉพาะ form ที่อยู่) -->

        <address-modal
                v-bind:title="lang.edit_buyer_information"
                v-bind:init-data="addressData"
                v-bind:member-profile="memberProfile"
                v-on:on-success-submit="onAddressSubmit"
                input-name=""
                v-bind:show="showAddressModal"
                v-bind:submitting="submittingAddressModal"
                v-on:on-add-address-modal-close="onAddAddressModalClose"
                v-bind:detect-random="true"
                v-bind:random="random"
                v-bind:email-required="false"
                ignore-validate-class="do-not-ignore"
                shop-name-id="edit_txt_customer_shop_name"
                first-name-id="edit_txt_customer_first_name"
                last-name-id="edit_txt_customer_last_name"
                telephone-number-id="edit_txt_customer_mobile_number"
                email-id="edit_txt_customer_email"
                address-id="edit_txt_customer_address"
                province-id="edit_cbo_customer_province"
                district-id="edit_cbo_customer_district"
                sub-district-id="edit_cbo_customer_subdistrict"
                postcode-id-name="edit_cbo_customer_postcode"
        ></address-modal>


    </div>
</template>

<script>
    import CartCheckoutAddress from './address/Address'
    import FullAddress from './address/FullAddress'
    import AddressModal from './address/AddressModal'
    import popup from '../../../libraries/popup'
    import {trans} from "../../../libraries/trans";

    export default {
        name: 'cart-checkout-buyer-information',
        props: {
            memberAddresses: {
                type: Object
            },
            memberProfile: {
                type: Object
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
            addressId: {
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
            selectAddressLabel: {
                type: String,
                default: ''
            },
            postcodeIdName: {
                type: String,
                default: ''
            }
        },
        components: {
            'cart-checkout-address': CartCheckoutAddress,
            'full-address': FullAddress,
            'address-modal': AddressModal
        },
        data: function () {
            return {
                showAddressModal: false,
                submittingAddressModal: false,
                address: this.memberAddresses,
                profile: this.memberProfile,
                random: 0
            }
        },
        methods: {
            onAddressSubmit: function (data) {
                var $this = this;
                this.submittingAddressModal = true;
                var postData = this.convertToFormData(data)
                postData['requiredEmail'] = 'no'
                axios.post(this.$store.getters.locale_url + '/members/postAddress', postData)
                    .then(function (response) {
                        if (typeof response.data.data == 'object' && typeof response.data.data.data == 'object') {
                            $this.address = response.data.data.data;
                            $this.address.first_name = response.data.profile.first_name
                            $this.address.last_name = response.data.profile.last_name
                            $this.address.contact_email = response.data.profile.email
                            $this.address.contact_phone = response.data.profile.phone
                            $this.profile = response.data.profile
                            $this.onAddAddressSuccess(response);
                        } else {
                            $this.onAddAddressError(response);
                        }
                    })
                    .catch(function (error) {
                        $this.onAddAddressError(error);
                    });
            },
            onAddAddressSuccess: function (response) {
                //popup.open('', this.lang.updated_buyer_information_successful, 'success', true)
                $.toast({
                    heading: this.lang.updated_buyer_information_successful,
                    position: 'top-right',
                    icon: 'success'
                });

                this.showAddressModal = false;
            },
            onAddAddressError: function (response) {
                popup.open('', trans('could_not_update_buyer_information', {code: response.data.error_code}), 'error', true)
                this.submittingAddressModal = false;
            },

            convertToFormData: function (data) {
                var newData = {
                    'id': this.address.id,
                    'first_name': data.first_name,
                    'last_name': data.last_name,
                    'phone': data.phone,
                    'email': data.email,
                    'address': data.address_line1,
                    'address2': data.address_line2,
                    'address3': '',
                    'subdistrict_id': data.subdistrict_id,
                    'district_id': data.district_id,
                    'province_id': data.province_id,
                    'postcode': data.postcode,
                    'address_name': data.shop_name,
                    'address_type': 'member'
                };

                return newData;
            },
            editAddress: function () {
                this.showAddressModal = true;
            },
            getAddressData: function () {

            },
            onAddAddressModalClose: function () {
                this.showAddressModal = false;
                this.submittingAddressModal = false;
                this.random = Math.random();

                this.$emit('on-close-address-modal')
            },

            replaceProfileToAddress()
            {
                if (!this.address) {
                    this.address = {}
                }

                this.address.first_name = (this.profile) ? this.profile.first_name : ''
                this.address.last_name = (this.profile) ? this.profile.last_name : ''
                this.address.contact_phone = (this.profile) ? this.profile.phone : ''
                this.address.contact_email = (this.profile) ? this.profile.email : ''

            }
        },
        mounted: function () {

        },
        computed: {
            lang: function () {
                return this.$store.getters.lang;
            },
            showInformationForm: function() {
                if((this.address && !_.isEmpty(this.address))
                    && (this.profile && !_.isEmpty(this.profile)))
                {
                    var address = this.addressData

                    if(
                        (!address.first_name || address.first_name == '')
                        || (!address.last_name || address.last_name == '')
                        || (!address.contact_phone || address.contact_phone == '')
                        || (!address.province_id || address.province_id == '')
                        || (!address.district_id || address.district_id == '')
                        || (!address.subdistrict_id || address.subdistrict_id == '')
                        || (!address.postcode || address.postcode == '')
                    ){
                        return false
                    }else{
                        return true
                    }
                    
                }

                return false
            },
            addressData()
            {
                this.replaceProfileToAddress()

                if (this.address
                    && typeof this.address.original_province == 'object'
                    && !_.isEmpty(this.address.original_province))
                {
                    this.address.province_id = this.address.original_province.id
                }

                if (this.address
                    && typeof this.address.original_district == 'object'
                    && !_.isEmpty(this.address.original_district))
                {
                    this.address.district_id = this.address.original_district.id
                }

                if (this.address
                    && typeof this.address.original_subdistrict == 'object'
                    && !_.isEmpty(this.address.original_subdistrict))
                {
                    this.address.subdistrict_id = this.address.original_subdistrict.id
                }

                return this.address
            }
        }
    }
</script>