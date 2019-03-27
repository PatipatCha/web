<template>
    <div class="section-head">
        <h4 v-if="showChangeAddressBtn" class="pay-pickup-text text-bold">
            <b>{{ trans('receiv_product_data') }}</b>
        </h4>
        <div class="row">
            <div class="col-xs-6 delivery-title" v-if="is_delivery">
                <div class="title text-pd-red text-bold">{{ trans('delivery_to_address') }}</div>
            </div>
            <div class="col-xs-6 delivery-title" v-else>
                <div class="title text-pd-red text-bold">{{ trans('pick_up_product_manually') }}</div>
            </div>
            <div v-if="showChangeAddressBtn" class="col-xs-6 text-right padding-right-15">
                <a
                        href="javascript:;"
                        class="change-shipping"
                        @click="changeAddress"
                        id="btn_step2_change_delivery_option"
                >
                    <i class="far fa-sync"></i> {{ trans('change_to_get_product') }}
                </a>
            </div>
            <!--<div v-if="!showChangeAddressBtn && selectedAddress.type == 'store'" class="col-xs-6 text-right padding-right-15">-->
                <!--<label>-->
                    <!--<input type="checkbox" id="checkbox_step3_pickup_all">-->
                    <!--{{ trans('pickup_all') }}-->
                <!--</label>-->
            <!--</div>-->
        </div>
        <div class="row">
            <div class="col-sm-12 panel-option">

                <div class="panel panel-default active" v-if="showSelectedAddressBox">
                    <div class="panel-body">
                        <div class="media">
                            <div class="media-left">
                                <i :class="boxIcon"></i>
                            </div>
                            <div class="media-body media-top">
                                <h4 class="media-heading">{{ addressName }}</h4>
                                <p>
                                    <full-address :address="ownerAddress" :join="' '"></full-address>
                                </p>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="shipping_address_id" :value="shippingAddressId"/>
                </div>

                <div v-if="!showSelectedAddressBox && showChangeAddressBtn">
                    <address-form
                            :use-delivery-residence-address="true"
                            input-name="new_shipping_address"
                            ignore-validate-class="first-do-not-ignore"
                            :init-data="address"
                            shop-name-id="txt_step2_shipping_shop_name"
                            first-name-id="txt_step2_shipping_first_name"
                            last-name-id="txt_step2_shipping_last_name"
                            telephone-number-id="txt_step2_shipping_mobile_number"
                            email-id="txt_step2_shipping_email"
                            address-id="txt_step2_shipping_address"
                            province-id="cbo_step2_shipping_province"
                            district-id="cbo_step2_shipping_district"
                            sub-district-id="cbo_step2_shipping_subdistrict"
                            btn-save-id="btn_step2_shipping_save"
                            btn-cancel-id="btn_step2_shipping_cancel"
                            label-first-name="lbl_step2_shipping_first_name"
                            label-last-name="lbl_step2_shipping_last_name"
                            label-mobile-number="lbl_step2_shipping_mobile_number"
                            label-email="lbl_step2_shipping_email"
                            label-address="lbl_step2_shipping_address"
                            label-province="lbl_step2_shipping_province"
                            label-district="lbl_step2_shipping_district"
                            label-sub-district="lbl_step2_shipping_subdistrict"
                            label-postcode="lbl_step2_shipping_postcode"
                            :email-required="false"
                    ></address-form>
                </div>

            </div>
        </div>

        <input type="hidden" name="delivery_type" :value="deliveryType"/>
        <input type="hidden" name="postcode" :value="postcode"/>
        <input type="hidden" name="sub_district_id" :value="sub_district_id"/>
    </div>
</template>

<script>
    import {mapState} from 'vuex'
    import FullAddress from '../../../checkout/address/FullAddress'
    import {SET_OPEN_POPUP_ADDRESS} from '../../../../../store/mutation-types'
    import AddressForm from '../../../checkout/address/Address'

    export default {
        name: 'store-shipping-address-container',
        components: {
            FullAddress,
            AddressForm
        },
        props: {
            addresses: {
                type: Array,
                default: []
            },
            showChangeAddressBtn: {
                type: Boolean,
                default: true
            }
        },
        data() {
            return {
                address: null,
                shipping_type: ''
            }
        },
        computed: {
            ...mapState({
                selectedAddress: state => state.storeModule.selected_address
            }),
            isSelectedAddress() {
                if (!_.isEmpty(this.selectedAddress)) {
                    return true
                }

                return false
            },
            showSelectedAddressBox() {
                if (this.selectedAddress) {
                    this.shipping_type = this.selectedAddress.type
                    if (this.shipping_type == 'store') {
                        return true
                    }
                }
                if (this.isSelectedAddress && !_.isEmpty(this.ownerAddress)) {
                    return true
                }

                return false
            },
            ownerAddress() {
                if (this.shipping_type == 'store') {
                    return this.selectedAddress
                }
                return this.addresses.find((item) => {
                    return item.id == _.get(this.selectedAddress, 'id')
                })
            },
            shippingAddressId() {
                if (this.shipping_type == 'store') {
                    return _.get(this.selectedAddress, 'store_id')
                }else {
                    return _.get(this.selectedAddress, 'id')
                }
            },
            addressName() {
                let name = _.get(this.ownerAddress, 'first_name', '') + ' ' + _.get(this.ownerAddress, 'last_name', '')

                if (this.shipping_type == 'store') {
                    name = _.get(this.selectedAddress.name, this.locale)
                }
                return name
            },
            is_delivery() {
                let delivery = false
                if (_.get(this.selectedAddress, 'type')) {
                    let type = _.get(this.selectedAddress, 'type')
                    if (type === 'address') {
                        delivery = true
                    }
                }
                return delivery
            },
            locale() {
                return this.$store.getters.locale
            },
            deliveryType() {
                if (this.selectedAddress.type == 'store') {
                    return 'pickup'
                }

                return 'shipping'
            },
            postcode() {
                return _.get(this.selectedAddress, 'postcode')
            },
            sub_district_id() {
                return _.get(this.selectedAddress, 'sub_districts.id')
            },
            boxIcon() {
                if(this.selectedAddress.type === 'store') {
                    return 'far fa-hand-holding-box'
                }else{
                    return 'far fa-truck-container'
                }
            }
        },
        methods: {
            changeAddress() {
                this.$store.commit(SET_OPEN_POPUP_ADDRESS, true)
            }
        },
        mounted() {
            if (!_.isEmpty(this.selectedAddress)) {
                const address = {
                    "id": "",
                    "first_name": "",
                    "last_name": "",
                    "contact_phone": "",
                    "contact_email": "",
                    "country_code": "",
                    "postcode": _.get(this.selectedAddress, 'postcode'),
                    "status": "",
                    "address_name": "",
                    "address": "",
                    "address2": "",
                    "address3": "-",
                    "subdistrict": "",
                    "district": "",
                    "province": "",
                    "location": null,
                    "original_subdistrict": {"id": _.get(this.selectedAddress, 'sub_districts.id', null)},
                    "original_district": {"id": _.get(this.selectedAddress, 'district.id', null)},
                    "original_province": {"id": _.get(this.selectedAddress, 'province.id', null)}
                }

                this.address = address
            }
        }
    }
</script>