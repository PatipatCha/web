<template>
    <div class="address-modal-box new-address-modal-box">
        <div class="title f-600">{{ addressLabel }}</div>
        <form method="post" action="#" ref="form">
            <address-form
                    :init-data="address"
                    :use-delivery-residence-address="true"
                    shop-name-id="txt_popup_shipping_shop_name"
                    first-name-id="txt_popup_shipping_first_name"
                    last-name-id="txt_popup_shipping_last_name"
                    telephone-number-id="txt_popup_shipping_mobile_number"
                    email-id="txt_popup_shipping_email"
                    address-id="txt_popup_shipping_address"
                    province-id="cbo_popup_shipping_province"
                    district-id="cbo_popup_shipping_district"
                    sub-district-id="cbo_popup_shipping_subdistrict"
                    btn-save-id="btn_popup_shipping_save"
                    btn-cancel-id="btn_popup_shipping_cancel"
                    label-first-name="lbl_popup_shipping_first_name"
                    label-last-name="lbl_popup_shipping_last_name"
                    label-mobile-number="lbl_popup_shipping_mobile_number"
                    label-email="lbl_popup_shipping_email"
                    label-address="lbl_popup_shipping_address"
                    label-province="lbl_popup_shipping_province"
                    label-district="lbl_popup_shipping_district"
                    label-sub-district="lbl_popup_shipping_subdistrict"
                    label-postcode="lbl_popup_shipping_postcode"
                    :email-required="false"
            ></address-form>
            <div class="error" v-if="error !== false">
                <br/>
                {{ error }}
            </div>
        </form>
        <div class="cta">
            <div class="pull-left">
                <button
                        type="button"
                        class="btn btn-default"
                        @click="() => { onCancel() }"
                        id="btn_popup_shipping_cancel"
                >
                    <i class="fas fa-arrow-left"></i> {{ trans('previous') }}
                </button>
            </div>

            <div class="pull-right">
                <button type="button"
                        class="btn btn-primary"
                        @click="submit"
                        id="btn_popup_shipping_save"
                >
                    <i class="fas fa-check" v-if="!submitting"></i>
                    <loading :show="submitting"></loading>
                    {{ trans('btn_confirm') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    import Modal from '../bootstrap/Modal2'
    import AddressForm from '../cart/checkout/address/Address'
    import {trans} from "../../libraries/trans";
    import {mapActions} from 'vuex'
    import Loading from '../loading/Loading'
    import { getAddressData } from '../../helpers/member'

    export default {
        name: 'shipping-address-form',
        components: {
            Modal,
            AddressForm,
            Loading
        },
        data() {
            return {
                showContent1: true,
                submitting: false,
                error: false
            }
        },
        props: {
            onSuccess: {
                type: Function,
                default: () => {}
            },
            address: {
                type: Object,
                default: null
            },
            onCancel: {
                type: Function,
                default: () => {}
            }
        },
        methods: {
            ...mapActions(['addAddress', 'updateAddress']),
            submit() {
                $(this.$refs.form).trigger('submit')
            },
            sendData() {
                const form = $(this.$refs.form);
                const datas = this.formatData(form.serializeArray())

                this.submitting = true
                axios.post(this.$store.getters.locale_url + '/members/postAddress', this.convertToFormData(datas))
                    .then((response) => {
                        if (_.isEmpty(this.getAddressId)) {
                            //Add
                            this.addAddress(_.get(response, 'data.data.data'))
                        } else {
                            //Update
                            this.updateAddress(_.get(response, 'data.data.data'))
                        }
                        this.submitting = false
                        this.onSuccess(getAddressData(_.get(response, 'data.data.data')))
                    })
                    .catch((err) => {
                        this.error = trans('could_not_add_shipping_address', {code: _.get(err.response, 'data.error_code')})
                        this.submitting = false

                    })

            },
            formatData: function (datas) {
                let newData = {};
                for (let i = 0; i < datas.length; ++i) {
                    newData[datas[i].name] = datas[i].value;
                }

                return newData;
            },
            convertToFormData: function (data) {
                const newData = {
                    'id': !_.isEmpty(this.getAddressId) ? this.getAddressId : null,
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
          initValidate() {
            const form = $(this.$refs.form);

            form.validate().destroy()

            _VALIDATOR_FORM = form
            _VALIDATOR = form.validate({
              submitHandler: (e) => {
                this.sendData()
                return false;
              },
              invalidHandler: (result) => {
                return false;
              }
            });
          }
        },
        computed: {
            getAddressId() {
                return _.get(this.address, 'id', null)
            },
            addressLabel() {
                if(!_.isEmpty(this.address)) {
                    return trans('edit_delivery_address')
                }else {
                    return trans('add_delivery_address')
                }
            }
        },
        mounted() {
          _IS_FORM_SUBMITTING = false

          const form = $(this.$refs.form);
          form.on('submit', () => {
            _IS_FORM_SUBMITTING = true
          })

          $('input,select,textarea', form).on('keyup change', () => {
            _IS_FORM_SUBMITTING = false
          })

          this.initValidate()
        }
    }
</script>