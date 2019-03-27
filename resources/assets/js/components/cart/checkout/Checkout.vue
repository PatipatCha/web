<template>
    <div class="container margin-bottom step-2">
        <div class="col-lg-12 cart-payment-bg in-cart-box">
            <form method="post" v-bind:action="submitUrl" ref="cart_checkout_form">

                <div class="row">
                    <div class="col-md-8 col-left">
                        <div class="box">
                            <store-shipping-address-container
                                    :addresses="shippingAddresses"
                            ></store-shipping-address-container>

                            <!-- ก้อน Shipping address -->
                            <!--<shipping-address item-column="col-sm-12"-->
                            <!--:checked-added-item="true"-->
                            <!--:addresses="shippingAddresses"-->
                            <!--type="cart"-->
                            <!--v-bind:selectable="true"-->
                            <!--v-bind:lang="lang"-->
                            <!--:can-delete="false"-->
                            <!--ref="shippingAddressRef"-->
                            <!--:use-delivery-residence-address="true"-->
                            <!--shop-name-id="txt_shop_name"-->
                            <!--first-name-id="txt_first_name"-->
                            <!--last-name-id="txt_last_name"-->
                            <!--telephone-number-id="txt_mobile_number"-->
                            <!--email-id="txt_email"-->
                            <!--address-id="txt_address"-->
                            <!--province-id="cbo_province"-->
                            <!--district-id="cbo_district"-->
                            <!--sub-district-id="cbo_subdistrict"-->
                            <!--btn-save-id="btn_save"-->
                            <!--btn-cancel-id="btn_cancel"-->
                            <!--label-first-name="lbl_first_name"-->
                            <!--label-last-name="lbl_last_name"-->
                            <!--label-mobile-number="lbl_mobile_number"-->
                            <!--label-email="lbl_email"-->
                            <!--label-address="lbl_address"-->
                            <!--label-province="lbl_province"-->
                            <!--label-district="lbl_district"-->
                            <!--label-sub-district="lbl_subdistrict"-->
                            <!--label-postcode="lbl_postcode"-->
                            <!--select-address-label="cart"-->
                            <!--:active-class="true"-->

                            <!--&gt;</shipping-address>-->
                            <!-- ก้อน Shipping address -->

                            <!-- ก้อน Payment method -->
                            <payment-method ref="PaymentMethodRef" input-name="payment_method"
                                            v-bind:default-payment-method="paymentMethod"
                                            v-bind:payment-methods="paymentMethods"></payment-method>
                            <!-- ก้อน Payment method -->


                            <!-- ก้อน Buyer Information (เฉพาะ form ที่อยู่) -->
                            <cart-checkout-buyer-information
                                    v-bind:member-addresses="memberAddresses"
                                    v-bind:member-profile="memberProfile"
                                    shop-name-id="txt_customer_shop_name"
                                    first-name-id="txt_customer_first_name"
                                    last-name-id="txt_customer_last_name"
                                    telephone-number-id="txt_customer_mobile_number"
                                    email-id="txt_customer_email"
                                    address-id="txt_customer_address"
                                    province-id="cbo_customer_province"
                                    district-id="cbo_customer_district"
                                    sub-district-id="cbo_customer_subdistrict"
                                    postcode-id-name="cbo_customer_postcode"
                                    btn-save-id="btn_save_buyer"
                                    btn-cancel-id="btn_cancel_buyer"
                                    label-first-name="lbl_customer_first_name"
                                    label-last-name="lbl_customer_last_name"
                                    label-mobile-number="lbl_customer_mobile_number"
                                    label-email="lbl_customer_email"
                                    label-address="lbl_customer_address"
                                    label-province="lbl_customer_province"
                                    label-district="lbl_customer_district"
                                    label-sub-district="lbl_customer_subdistrict"
                                    label-postcode="lbl_customer_postcode"
                                    @on-close-address-modal="onCloseBuyerAddressModal"
                            ></cart-checkout-buyer-information>
                            <!-- ก้อน Buyer Information (เฉพาะ form ที่อยู่) -->

                            <!-- ก้อน ใบกำกับภาษี (Invoice) -->
                            <cart-checkout-invoice-address
                                    v-bind:address="taxAddress"
                                    v-bind:billing-address="billingAddress"
                                    v-bind:makro-card-info="makroCardInfo"
                                    v-bind:has-makro-card="hasMakroCard"
                                    v-bind:is-created-siebel="isCreatedSiebel"
                                    v-bind:member-was-ordered="memberWasOrdered"
                                    v-bind:buyer-address="memberAddresses"
                            ></cart-checkout-invoice-address>
                            <!-- ก้อน ใบกำกับภาษี (Invoice) -->

                            <div class="clearfix"></div>

                            <div class="alert alert-danger" v-if="false">
                                <strong>{{ lang.could_not_continue_proceeding }}</strong><br/><br/>
                                {{ lang.please_check_following_error_above }}
                            </div>
                            <!--<div class="btn-box">-->
                            <!--<button class="btn-continue" type="submit" disabled ref="submitBtn" id="btn_checkout_button">{{ lang.continue }} <loader v-bind:show="submitting"></loader></button>-->
                            <!--</div>-->
                        </div>
                    </div>

                    <div class="col-md-4 col-right cart-summary-sidebar">
                        <div class="box">
                            <!-- ก้อน Cart detail summary -->
                            <cart-checkout-summary :config-date="configDate" v-bind:cart="cart" v-bind:promotions="promotions" v-bind:show-status="this.showItemStatus"></cart-checkout-summary>
                            <!-- ก้อน Cart detail summary -->

                            <summary-price :on-submit="onSubmitForm" :submitting="submitting"
                                           v-bind:summary="cartSummary"></summary-price>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="makro_card_id" v-bind:value="memberProfile.makro_card_id"/>
            </form>

            <form id="continue-shipping-form" ref="continueShippingForm" method="post" v-bind:action="shippingFormUrl">
                <input type="hidden" name="submit_form_payment" value="1">
                <input type="hidden" name="reserve_id" v-bind:value="reserveId">
            </form>
        </div>

        <modal
                v-bind:show="unavailableProduct"
                additional-custom-classes="address-modal-box"
                v-on:on-close-modal="onCloseModal"
                v-bind:title="lang.product_not_available_for_buying"
                content-size="full"
                v-bind:center="false"
                :prevent-close="true"
        >
            <div>
                {{ lang.product_not_available_for_buying_message }}
                <div class="scroller">
                    <product-list v-bind:items="unavailableProductData"></product-list>
                </div>

                <div v-if="isProductEmpty" class="text-danger text-center">
                    <br/>
                    {{ lang.no_product_to_buy_please_choose_product_again }}
                </div>
                <div class="clear-fix"></div>
                <div class="text-center" v-if="confirmation_proceed_error !== false">
                    <label class="error">{{ confirmation_proceed_error }}</label>
                </div>
            </div>

            <div slot="footer" class="btn-wrap">
                <!--<button class="btn btn-close" type="button" v-on:click="continueShopping">{{ lang.continue_choose_product }}</button>-->
                <button class="btn btn-primary" v-on:click="continueProceedToShipping">
                    <loader v-bind:show="submitting_confirmation_proceed"></loader>
                    {{ lang.confirm_to_proceed }}
                </button>
            </div>
        </modal>

    </div>
</template>

<script>
    import 'jquery-validation'
    import PaymentMethod from './PaymentMethod'
    import CartSummary from '../detail/Summary'
    import cartCheckoutBillingAddress from  './BillingAddress'
    import cartCheckoutInvoiceAddress from  './InvoiceAddress'
    import { SET_CART_SUMMARY, SET_CART_ITEMS, SET_OPEN_POPUP_ADDRESS, SET_ERROR_STORE_NOTAVILABLE_MESSAGE } from '../../../store/mutation-types'
    import BuyerInformation from './BuyerInformation'
    import CartCheckoutSummary from './summary/Summary'
    import Loader from '../../loading/Loading'
    import SummaryPrice from './SummaryPrice'
    import Modal from '../../bootstrap/Modal2'
    import ProductList from './product_type/list/List'
    import popup from '../../../libraries/popup'
    import GoogleAnalytic from '../../../libraries/google_analytic'
    import ShippingAddress from './shipping_address/Container'
    import {trans} from "../../../libraries/trans";
    import StoreShippingAddressContainer from '../checkout/shipping_address/store/Container'
    import Scroll from '../../../libraries/Scroll'
    import { mapActions, mapState } from 'vuex'

    const googleAnanlytic = new GoogleAnalytic()
    const scroll = new Scroll

    export default {
        name: 'cart-checkout',
        props: [
            'cart',
            'cartSummary',
            'currentStore',
            'promotions',
            'submitUrl',
            'memberAddresses',
            'billingAddress',
            'taxAddress',
            'shippingFormUrl',
            'paymentMethod',
            'paymentMethods',
            'memberProfile',
            'makroCardInfo',
            'hasMakroCard',
            'isCreatedSiebel',
            'memberWasOrdered',
            'shippingAddresses',
            'showItemStatus',
            'configDate'
        ],
        components: {
            StoreShippingAddressContainer,
            'payment-method': PaymentMethod,
            'cart-summary': CartSummary,
            'cart-checkout-billing-address': cartCheckoutBillingAddress,
            'cart-checkout-invoice-address': cartCheckoutInvoiceAddress,
            'cart-checkout-buyer-information': BuyerInformation,
            'cart-checkout-summary': CartCheckoutSummary,
            'loader': Loader,
            'summary-price': SummaryPrice,
            'modal': Modal,
            'product-list': ProductList,
            'shipping-address': ShippingAddress,
        },
        data: function () {
            return {
                url: this.$store.getters.url + '/',
                locale_url: this.$store.getters.locale_url + '/',
                validate_error: false,
                submitting: false,
                unavailableProduct: false,
                unavailableProductData: [],
                isProductEmpty: false,
                reserveId: null,
                confirmation_proceed_error: false,
                submitting_confirmation_proceed: false
            }
        },
        mounted: function () {
            //Send GA (Payment landing (Step 0))
            let products = this.getProducts()
            googleAnanlytic.checkoutPaymentLanding(products, () => {
                console.log('ga: Checkout payment step-1 callback')
            })

            this.$store.commit(SET_CART_SUMMARY, this.cartSummary);
            var me = this;
            var form = $(this.$refs.cart_checkout_form);
            this.$store.commit(SET_CART_ITEMS, this.cart);

            jQuery.validator.addMethod("has_payment_method", function(value, element) {
                console.log(me.$refs.PaymentMethodRef.payment_id)
                if (me.$refs.PaymentMethodRef.payment_id
                    && me.$refs.PaymentMethodRef.payment_id.length > 0
                )
                {
                    return true
                }

                return false
            }, me.lang.please_select_payment_method);

            jQuery.validator.addMethod("has_shipping_address", function(value, element) {
                if (me.$refs.shippingAddressRef.userSelectShippingAddressId
                    && me.$refs.shippingAddressRef.userSelectShippingAddressId.length > 0
                )
                {
                    return true
                }

                return false
            }, me.lang.please_select_delivery_address);



            form.validate({
                rules: {
                    // 'shipping_address_id': {
                    //     'has_shipping_address': function () {
                    //         return true
                    //     }
                    // },
                    'payment_method[payment_method]': {
                        'has_payment_method': function () {
                            return true
                        }
                    }
                },
                ignore: ':hidden:not(.first-do-not-ignore)',
                submitHandler: function (e) {
                    if (!me.additionalValidate()) {
                        me.validate_error = true;
                        $(me.$refs.submitBtn).prop('disabled', false).removeAttr('disabled');
                        return false;
                    }

                    me.validate_error = false;
                    $(me.$refs.submitBtn).prop('disabled', true).attr('disabled', 'disabled');

                    me.submitForm();
                    return false;
                },
                invalidHandler: function (result, validator) {
                    // $('label[for="required_province_id"]').attr('id', 'lbl_province')
                    me.additionalValidate();
                    me.validate_error = true;
                    scroll.to(document.querySelector('#lbl_select_delivery_address.error'))
                },
                messages: {
                    'payment_method': {
                        'has_payment_method': me.lang.please_select_payment_method
                    },
                    'shipping_address_id': {
                        'has_shipping_address': me.lang.please_select_delivery_address
                    }
                }
            });

            $(this.$refs.submitBtn).removeAttr('disabled')
        },
        methods: {
            ...mapActions([
                'updateCurrentStore',
                'setSelectedAddress'
            ]),
            getProducts: function () {
                let products = []
                for (let i = 0; i < this.cart.length; ++i) {
                    for (let j = 0; j < this.cart[i].items.length; ++j) {
                        let product = _.get(this.cart[i].items[j], 'content.data', {})
                        product.qty = _.get(this.cart[i].items[j], 'quantity')
                        product.price = _.get(this.cart[i].items[j], 'price.data.price')
                        products.push(product)
                    }
                }
                return products
            },
            onAddDeliveryAddress: function (data) {

            },

            additionalValidate: function () {
                var passed = true;

        if (typeof this.cart.non_assortment_installment == 'object') {
          var nonAssortmentNoInstallmentPassed = this.$refs.non_assortment_no_installment.isValid()
          passed = passed && nonAssortmentNoInstallmentPassed;

          var deliveryMethodPassed = this.$refs.non_assortment_no_installment.isSelectDeliveryMethod();
          passed = passed && deliveryMethodPassed;
        }
        return passed;
      },

      submitForm: function () {
        console.log('submitForm')
        var form = $(this.$refs.cart_checkout_form);

        this.submitting = true;

        axios.post(this.submitUrl, form.serialize())
          .then(this.onSubmitSuccess)
          .catch(this.onSubmitError);
      },

      onSubmitComplete: function () {
        $(this.$refs.submitBtn).prop('disabled', false).removeAttr('disabled');
        this.submitting = false;
      },

      onSubmitSuccess: function (response) {
        var me = this;
        if (response.data.status == 'ok') {
          this.handleCreatedShippingAddress(response)

          let products = this.getProducts()
          googleAnanlytic.checkoutPayment(products, response.data.payment_method, () => {
            this.reserveId = response.data.reserve_id;
            window.location = response.data.next_url;
          })
        } else {
          //Error
          this.onSubmitError(response);
        }
      },

      handleCreatedShippingAddress(response) {
        let deliveryStoreAddress = _.get(response.data, 'delivery_store_address')
        let createdShippingAddress = _.get(response.data, 'created_shipping_address')

        if (!_.isEmpty(deliveryStoreAddress) && !_.isEmpty(createdShippingAddress)) {
          const memberAddress = _.get(createdShippingAddress, 'data')
          this.doSetSelectedNewAddress(this.convertDeliveryStoreToAddress(deliveryStoreAddress, memberAddress))
        }
      },

      onSubmitForm() {
        if (!this.submitting) {
          $(this.$refs.cart_checkout_form).trigger('submit')
        }
      },

      onSubmitError: function (response) {
        console.log(response)
        //this.reserveId = null;
        var me = this
        this.onSubmitComplete();
        if (typeof response.data && typeof response.data.has_unavailable_product == 'number' && response.data.has_unavailable_product == 1) {
          this.unavailableProduct = true;
          var cartData = this.getCartData(response.data.unavailable_products);
          this.unavailableProductData = cartData;
          this.checkReserveProductEmpty(response);
          this.reserveId = response.data.reserve_id;
        } else if (_.get(response.data, 'delivery_store_address_is_changed') === 1) {
          popup.open({
            type: 'confirm',
            message: this.trans('confirm_change_delivery_address_message'),
            confirm() {
              me.updateDeliveryStoreAddress(_.get(response.data, 'address'))
            }
          })
        } else if (_.get(response.data, 'error_code') === 221001) {
          me.$store.commit(SET_OPEN_POPUP_ADDRESS, true)
          // me.$store.commit(SET_ERROR_STORE_NOTAVILABLE_MESSAGE, response.data.message)
        } else if (typeof response.data == 'object' && typeof response.data.message == 'string') {
          let message = this.lang.could_not_reserve_message
          if (response.data.display_error && response.data.display_error === 1) {
            message = response.data.message
          }
          if (response.data.status === 'error_connect') {
            popup.open({
              type: 'error',
              title: this.lang.could_not_proceeding_to_shipping_step,
              message: _.get(response, 'data.message', message),
              confirm: function () {
                window.location.reload()
              },
              confirmText: this.lang.reload_page,
              preventClose: true
            })
          } else if (response.data.error_ma === 1) {
            popup.open({
              type: 'error',
              title: this.lang.could_not_proceeding_to_shipping_step,
              message: _.get(response, 'data.message', message),
              confirm: function () {
                window.location = response.data.next_url;
              },
              confirmText: this.lang.reload_page,
              preventClose: true
            })
          } else {
            if (response.data.error_code) {
              message = trans('could_not_reserve_message_with_code', {code: response.data.error_code})
            }

            if (_.get(response, 'data.use_custom_message') == 1) {
              message = _.get(response, 'data.message', trans('could_not_reserve_message_with_code', {code: response.data.error_code}))
            }

            let confirmText = this.lang.back_to_home
            switch (_.get(response, 'data.action')) {
              case 'close_popup':
                confirmText = this.lang.ok
                break;
            }

            popup.open({
              type: 'error',
              title: this.lang.could_not_proceeding_to_shipping_step,
              message: message,
              confirm: function (callback) {
                switch (_.get(response, 'data.action')) {
                  case 'close_popup':
                    popup.close()
                    callback()
                    break;
                  default:
                    window.location = me.$store.getters.locale_url
                }

              },
              confirmText: confirmText,
              showLoading: false
            })
          }
        }
      },

      checkReserveProductEmpty: function (response) {
        this.isProductEmpty = response.data.is_product_empty == 1 ? true : false
      },

      getCartData: function (products) {
        var cartData = [];
        for (var i = 0; i < products.length; ++i) {
          let item = {
            ...products[i]
          }

          delete item.item
          products[i].item.info = item
          cartData.push(products[i].item)
        }

        return cartData;
      },
      getProductById: function (productId) {
        for (var i in this.cart) {
          for (var j = 0; j < this.cart[i].items.length; ++j) {
            if (this.cart[i].items[j].content_id == productId) {
              return this.cart[i].items[j];
            }
          }
        }

        return null;
      },
      onCloseModal: function () {
        this.unavailableProduct = false;
      },
      continueShopping: function () {
        window.location = this.$store.getters.locale_url;
      },
      continueProceedToShipping: function () {


        // window.location = this.shippingFormUrl
        this.manageCartItems()
      },

      manageCartItems() {
        this.confirmation_proceed_error = false
        let updateItems = []
        let removeItems = []
        for (let i = 0; i < this.unavailableProductData.length; ++i) {
          console.log(this.unavailableProductData[i])
          switch (this.unavailableProductData[i].info.error) {
            case 'unpublished':
              removeItems.push({
                'id': this.unavailableProductData[i].info.basket_id
              })
              break;
            case 'not_enough_inventory':
              updateItems.push({
                'id': this.unavailableProductData[i].info.basket_id,
                'qty': this.unavailableProductData[i].info.available
              })
              break;
          }
        }

        const param = {
          'unpublished': removeItems,
          'not_enough_inventory': updateItems
        }

        this.submitting_confirmation_proceed = true
        axios.post(this.$store.getters.locale_url + '/carts/update-items', param)
          .then((response) => {
            if (response.data.status != 'success') {
              this.submitting_confirmation_proceed = false
              this.confirmation_proceed_error = response.data.message
            } else {
              if (this.$store.getters.cartCount - removeItems.length > 0) {
                this.submitForm()
              } else {
                window.location = this.$store.getters.locale_url + '/carts'
              }
            }
          })
          .catch((error) => {
            this.submitting_confirmation_proceed = false
            this.confirmation_proceed_error = this.lang.could_not_confirmation_to_proceed
          })
      },
      updateDeliveryStoreAddress(storeAddress) {
        const payload = {
          store_id: storeAddress.main_inventory_store,
          store_price_id: storeAddress.store_price
        }

        axios.post(`${this.$store.getters.locale_url}/carts/move-cart`, payload)
          .then((response) => {
            if (_.get(response, 'data.status') == 'success') {
              popup.setErrorText('')
              this.doSetSelectedAddress(payload)

              this.$nextTick(() => {
                window.location = this.$store.getters.locale_url + '/carts'
              })
            } else {
              //TODO: handle error later
              popup.setErrorText(_.get(response, 'data.message', this.lang.could_not_confirmation_to_proceed))
            }
          })
          .catch((err) => {
            //TODO: handle error later
            popup.setErrorText(_.get(err, 'response.data.message', this.lang.could_not_confirmation_to_proceed))
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
          'id': null,
          'first_name': data['new_shipping_address[first_name]'],
          'last_name': data['new_shipping_address[last_name]'],
          'phone': data['new_shipping_address[phone]'],
          'email': data['new_shipping_address[email]'],
          'address': data['new_shipping_address[address_line1]'],
          'address2': '',
          'address3': '',
          'subdistrict_id': data['new_shipping_address[subdistrict_id]'],
          'district_id': data['new_shipping_address[district_id]'],
          'province_id': data['new_shipping_address[province_id]'],
          'postcode': data['new_shipping_address[postcode]'],
          'address_name': data['new_shipping_address[shop_name]'],
          'requiredEmail': true
        };

        return newData;
      },

      convertDeliveryStoreToAddress(storeAddress, memberAddress) {
        const newAddress = {
          'id': _.get(memberAddress, 'id'),
          "sub_districts": {
            "id": _.get(memberAddress, 'original_subdistrict.id'),
            "name": _.get(memberAddress, 'subdistrict'),
            "original_name": {
              "th": _.get(memberAddress, 'original_subdistrict.th'),
              "en": _.get(memberAddress, 'original_subdistrict.en')
            }
          },
          "district": {
            "id": _.get(memberAddress, 'original_district.id'),
            "name": _.get(memberAddress, 'district'),
            "original_name": {
              "th": _.get(memberAddress, 'original_district.th'),
              "en": _.get(memberAddress, 'original_district.en')
            }
          },
          "province": {
            "id": _.get(memberAddress, 'original_province.id'),
            "name": _.get(memberAddress, 'province'),
            "original_name": {
              "th": _.get(memberAddress, 'original_province.th'),
              "en": _.get(memberAddress, 'original_province.en')
            }
          },
          "postcode": _.get(storeAddress, 'postcode'),
          "main_inventory_store": _.get(storeAddress, 'main_inventory_store'),
          "store_price": _.get(storeAddress, 'store_price')
        }

        return newAddress
      },

      doSetSelectedAddress(store) {
        let newSelectedAddress = _.cloneDeep(this.selectedAddress)
        newSelectedAddress.main_inventory_store = store.store_id
        newSelectedAddress.store_price = store.store_price_id
        // let payload = address
        // payload.id = _.get(address, 'id', null)
        // payload.type = 'address'
        this.setSelectedAddress(newSelectedAddress)
      },

      doSetSelectedNewAddress(address) {
        let payload = address
        payload.id = _.get(address, 'id', null)
        payload.type = 'address'
        this.setSelectedAddress(payload)
      },

      initValidate() {
        console.log('Checkout initValidate')
        var me = this
        var form = $(this.$refs.cart_checkout_form);
        //form.validate().destroy()

        _VALIDATOR_FORM = form
        _VALIDATOR = form.validate({
          rules: {
            // 'shipping_address_id': {
            //     'has_shipping_address': function () {
            //         return true
            //     }
            // },
            'payment_method[payment_method]': {
              'has_payment_method': function () {
                return true
              }
            }
          },
          ignore: ':hidden:not(.first-do-not-ignore)',
          submitHandler: function (e) {
            if (!me.additionalValidate()) {
              me.validate_error = true;
              $(me.$refs.submitBtn).prop('disabled', false).removeAttr('disabled');
              return false;
            }

            me.validate_error = false;
            $(me.$refs.submitBtn).prop('disabled', true).attr('disabled', 'disabled');

            me.submitForm();
            return false;
          },
          invalidHandler: function (result, validator) {
            // $('label[for="required_province_id"]').attr('id', 'lbl_province')
            me.additionalValidate();
            me.validate_error = true;
            scroll.to(document.querySelector('#lbl_select_delivery_address.error'))
          },
          messages: {
            'payment_method': {
              'has_payment_method': me.lang.please_select_payment_method
            },
            'shipping_address_id': {
              'has_shipping_address': me.lang.please_select_delivery_address
            }
          }
        });
      },
      onCloseBuyerAddressModal() {
        this.initValidate()
      }
    },
    computed: {
      ...mapState({
        selectedAddress: state => state.storeModule.selected_address,
        open_popup_address: state => state.storeModule.open_popup_address
      }),
      assortmentData: function () {
        if (typeof this.cart.assortment == 'object') {
          return this.cart.assortment;
        }
        return false;
      },
      nonAssortmentInstallmentData: function () {
        if (typeof this.cart.non_assortment_with_installment == 'object') {
          return this.cart.non_assortment_with_installment;
        }
        return false;
      },
      nonAssortmentNoInstallmentData: function () {
        if (typeof this.cart.non_assortment_without_installment == 'object') {
          return this.cart.non_assortment_without_installment;
        }
        return false;
      },
      lang: function () {
        return this.$store.getters.lang;
      }
    },
    watch: {
      open_popup_address: function (value) {
        if (value === false) {
            this.initValidate()
        }
      }
    }
  };
</script>