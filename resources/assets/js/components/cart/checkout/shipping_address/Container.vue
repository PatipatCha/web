<template>
    <div>

        <!-- Label ของหน้า cart -->
        <div v-if="type == 'cart'">
            <h4 class="pay-pickup-text text-bold">
                <b>{{ lang.shipping_address_label }}</b>
            </h4>

            <label>{{ lang.select_delivery_address }}</label><br>
            <label class="error" for="shipping_address_id" id="lbl_select_delivery_address"></label>
        </div>
        <!-- Label ของหน้า cart -->

        <div class="margin-bottom-15">
            <button class="btn" id="btn_profile_add_new_delivery_address" v-bind:class="addBtnExistAddressClass" v-if="addressData.length > 0 && type == 'profile'" v-on:click="addAddress" type="button">{{ lang.add_address }}
            </button>
        </div>

        <!-- Empty ของหน้า profile -->
        <div class="empty-box text-center" v-if="addressData.length < 1 && type == 'profile'">
            <img :src="url + 'assets/images/icon-address-100px.png'"/>
            <h4>{{ lang.empty_shipping_address }}</h4>
            <a class="btn btn-success" id="btn_profile_add_new_delivery_address" v-on:click="addAddress">{{ lang.add_address }}</a>
        </div>


        <!-- Empty ของหน้า cart (step 3) -->
        <!--<div class="empty-box" v-if="addressData.length < 1 && type == 'cart'">-->
        <!--<a class="btn btn-success" v-on:click="addAddress">{{ lang.add_address }}</a>-->
        <!--</div>-->


        <div class="row flex-item" v-if="addressData.length > 0">
            <item
                    v-bind:lang="lang"
                    v-bind:item="item"
                    v-bind:index="index"
                    v-on:on-edit="onEdit"
                    v-on:on-remove="onRemove"
                    v-bind:selectable="selectable"
                    :key="item.id"
                    v-for="(item, index) in addressData"
                    :selected-shipping-address-id="selectedShippingAddressId"
                    :column="itemColumn"
                    :can-delete="canDelete"
                    @select-shipping-address="onSelectShippingAddress"
                    :select-address-label="selectAddressLabel"
                    :active-class="activeClass"
            ></item>
        </div>

        <div v-if="addressData.length < 1">
            <input type="radio" name="shipping_address_id" value="" style="visibility: hidden;"/>
        </div>


        <!-- ปุ่มเพิ่มที่อยู่สำหรับหน้า cart -->
        <button class="btn btn-primary" v-bind:class="addBtnExistAddressClass" v-if="type == 'cart'"
                v-on:click="addAddress" type="button" id="btn_profile_add_new_delivery_address">{{ lang.add_address }}
        </button>
        <!-- ปุ่มเพิ่มที่อยู่สำหรับหน้า cart -->


        <address-modal
                form-id="shipping_form"
                v-bind:show="showAddAddressModal"
                v-bind:title="title"
                v-on:on-success-submit="onSubmitAddress"
                v-on:on-add-address-modal-close="onAddAddressModalClose"
                v-bind:submitting="submitting"
                v-bind:init-data="editItem"
                v-bind:lang="lang"
                :email-required="false"
                :use-delivery-residence-address="useDeliveryResidenceAddress"
                :shop-name-id="shopNameId"
                :first-name-id="firstNameId"
                :last-name-id="lastNameId"
                :telephone-number-id="telephoneNumberId"
                :email-id="emailId"
                :address-id="addressId"
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
        >
        </address-modal>
    </div>
</template>

<script>
    import Add from './Add'
    import List from './List'
    import AddressModal from '../address/AddressModal'
    import Item from './ListItem'
    import popup from '../../../../libraries/popup'
    import {trans} from "../../../../libraries/trans";

    export default {
        name: 'shipping-address-container',
        components: {
            Add,
            List,
            AddressModal,
            Item
        },
        props: {
            addresses: {
                type: Array,
                default: []
            },
            type: {
                type: String,
                default: 'profile' // 'profile', 'cart'
            },
            selectable: {
                type: Boolean,
                default: false
            },
            checkedAddedItem: {
                type: Boolean,
                default: false
            },
            itemColumn: {
                type: String,
                default: 'col-sm-6'
            },
            canDelete: {
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
            activeClass: {
                type: Boolean,
                default: false
            }

        },
        data: function () {
            return {
                url: this.$store.getters.url + '/',
                showAddAddressModal: false,
                submitting: false,
                editItem: {},
                title: '',
                addressData: this.addresses,
                currentIndex: null,
                selectedShippingAddressId: '',
                userSelectShippingAddressId: null
            }
        },
        methods: {
            onSelectShippingAddress(id) {
                this.userSelectShippingAddressId = id
                this.selectedShippingAddressId = id
            },

            getShippingAddresses: function () {
                var $this = this;
                axios.get(this.$store.getters.locale_url + '/members/shipping-list')
                    .then(function (response) {
                        $this.addresses = response.data.items;
                    })
                    .catch(function () {
                        $this.addresses = [];
                    })
            },
            addAddress: function () {
                this.editItem = {};
                this.title = this.lang.add_shipping_address;
                this.showAddAddressModal = true;
            },
            onAddAddressModalClose: function () {
                this.showAddAddressModal = false
            },
            onSubmitAddress: function (data) {
                this.sendSubmitAddress(data);
            },
            sendSubmitAddress: function (data) {
                var $this = this;
                this.submitting = true;

                axios.post(this.$store.getters.locale_url + '/members/postAddress', this.convertToFormData(data))
                    .then(function (response) {
                        $this.submitting = false;
                        if (typeof response.data.data == 'object' && typeof response.data.data.data == 'object') {
                            $this.showAddAddressModal = false;
                            if ($this.editItem && typeof $this.editItem.id == 'string') {
                                //Edit
                                //console.log('index', $this.currentIndex);
                                $this.addressData[$this.currentIndex] = response.data.data.data;
                                // popup.open('', $this.lang.edited_shipping_address_successful, 'success', true)

                                $.toast({
                                    heading: $this.lang.edited_shipping_address_successful,
                                    position: 'top-right',
                                    icon: 'success'
                                });
                            } else {
                                //Add
                                $this.addressData.push(response.data.data.data);
                                // popup.open('', $this.lang.added_shipping_address_successful, 'success', true)
                                if ($this.checkedAddedItem) {
                                    $this.selectedShippingAddressId = response.data.data.data.id
                                }

                                $.toast({
                                    heading: $this.lang.added_shipping_address_successful,
                                    position: 'top-right',
                                    icon: 'success'
                                });
                            }
                        } else {
                            $this.onUpdateError(response)
                        }

                    })
                    .catch(function (error) {
                        $this.submitting = false;
                        if ($this.editItem && typeof $this.editItem.id == 'string') {
                            popup.open('', trans('could_not_edit_shipping_address'), 'error', true)
                        } else {
                            popup.open('', trans('could_not_add_shipping_address'), 'error', true)
                        }

                    });
            },
            convertToFormData: function (data) {
                var newData = {
                    'id': (this.editItem && typeof this.editItem.id == 'string') ? this.editItem.id : null,
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
                    'requiredEmail': true
                };

                return newData;
            },
            onEdit: function (index) {
                var item = this.getItem(index);
                this.editItem = item;
                this.title = this.lang.edit_shipping_address;
                this.showAddAddressModal = true;
                this.currentIndex = index;
            },
            onRemove: function (index) {
                var item = this.getItem(index);
                var id = item.id;
                var $this = this
                // this.addressData.splice(index, 1);
                popup.open(this.lang.are_you_sure, this.lang.to_remove_this_address, 'confirm', true, function (done) {
                    axios.post($this.$store.getters.locale_url + '/members/removeShipping', {
                        'id': id
                    })
                        .then(function (response) {
                            done()
                            popup.close()
                            if (typeof response.data.data.status == "string") {
                                if (response.data.data.status === "error") {
                                    popup.open('', trans('could_not_remove_shipping_address', {code: response.data.data.error_code}), 'error', true)
                                } else {
                                    $this.addressData.splice(index, 1);
                                    $.toast({
                                        heading: $this.lang.your_shipping_address_has_been_removed,
                                        position: 'top-right',
                                        icon: 'success'
                                    });
                                }
                            } else {

                            }
                        })
                        .catch(function () {

                        });
                }, null, false, 'btn_pop_up_confirm_delete_delivery')

            },
            getItem: function (index) {
                return this.addressData[index];
            },
            onUpdateError: function (response) {
                if (this.editItem && typeof this.editItem.id == 'string') {
                    popup.open('', trans('could_not_edit_shipping_address', {code: response.data.error_code}), 'error', true)
                } else {
                    popup.open('', trans('could_not_add_shipping_address', {code: response.data.error_code}), 'error', true)
                }
            }
        },
        mounted: function () {
            this.title = this.lang.add_shipping_address
            //this.getShippingAddresses();
        },
        computed: {
            addBtnExistAddressClass: function () {
                //btn-success btn-add-address
                var btnSuccess = false;
                if (this.type == 'profile') {
                    btnSuccess = true;
                }
                return {
                    'btn-success': btnSuccess,
                    'btn-primary': btnSuccess
                }
            },
            lang: function () {
                return this.$store.getters.lang
            },
            isSelected() {

            }
        }
    }
</script>