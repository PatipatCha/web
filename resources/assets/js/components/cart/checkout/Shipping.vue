<template>
    <div class="container margin-bottom">
        <div class="col-lg-12 cart-payment-bg in-cart-box step-3">
            <div class="row">
                <div class="col-md-8 col-left">
                    <div class="box">
                        <form method="post" ref="form" v-bind:action="submitUrl">
                            <div class="pay-pickup-text text-bold">
                                <b>{{ lang.shipping_information }}</b>
                            </div>
                            <div class="box">
                                <store-shipping-address-container :show-change-address-btn="false"
                                                                  :addresses="shippingAddresses"></store-shipping-address-container>
                            </div>
                            <div class="pd-assortmrnt">
                                <div class="pay-pickup">
                                    <group-items
                                            v-if="data.items.length > 0"
                                            v-bind:title="data.title"
                                            v-bind:alias="data.alias"
                                            v-bind:items="data.items"
                                            v-for="data in cart"
                                            :key="data.id"
                                            :can-edit="false"
                                            :delivery-dates="deliveryDates"
                                            :description="data.shipping_description"
                                            :btn-id="data.id_btn"
                                            :delivery-date-label="data.delivery_date_label"
                                            :calculate-date="true"
                                            :config-date="configDate"
                                    ></group-items>
    
                                    <div v-if="selectedAddress.type !== 'store'" v-html="lang.delivery_remark"></div>
    
                                    <!-- Assortment -->
                                    <!--<product-assortment-->
                                    <!--v-if="assortmentData"-->
                                    <!--v-bind:group-data="assortmentData"-->
                                    <!--v-bind:current-store="currentStore"-->
                                    <!--&gt;-->
                                    <!--</product-assortment>  -->
    
    
                                    <!-- Non assortment (Without installment) -->
                                    <!--<product-non-assortment-no-installment-->
                                    <!--v-if="nonAssortmentNoInstallmentData"-->
                                    <!--v-bind:group-data="nonAssortmentNoInstallmentData"-->
                                    <!--v-bind:current-store="currentStore"-->
                                    <!--&gt;-->
                                    <!--</product-non-assortment-no-installment> -->
    
    
                                    <!-- Non assortment (With installment) -->
                                    <!--<product-non-assortment-installment-->
                                    <!--v-if="nonAssortmentInstallmentData"-->
                                    <!--v-bind:group-data="nonAssortmentInstallmentData"-->
                                    <!--v-on:add-delivery-address="onAddDeliveryAddress"-->
                                    <!--ref="non_assortment_no_installment"-->
                                    <!--v-bind:member-address="memberAddress"-->
                                    <!--v-bind:shipping-addresses="shippingAddresses"-->
                                    <!--&gt;-->
                                    <!--</product-non-assortment-installment>-->
    
                                </div>
                            </div>
    
                            <div class="clearfix"></div>
    
                            <div class="alert alert-danger" v-if="false">
                                <strong>{{ lang.could_not_continue_proceeding }}</strong><br/><br/>
                                {{ lang.please_check_following_error_above }}
                            </div>
    
                            <!--<div class="btn-box">-->
                            <!--<button class="btn-continue" disabled type="submit" ref="submitBtn" id="btn_place_your_order">{{ lang.payment_btn }} <loading v-bind:show="submitting"></loading></button>-->
                            <!--</div>-->
    
                            <input type="hidden" name="postcode" :value="selectedAddress.postcode"/>
                            <input type="hidden" name="sub_district_id" :value="subDistrictId"/>
                            <input type="hidden" name="address_id" :value="addressId"/>
                            <input type="hidden" name="address_type" :value="addressType"/>
                        </form>
                    </div>
                </div>

                <div class="col-md-4 cart-summary-sidebar col-right">
                    <div class="box">
                        <!-- ก้อน Cart detail summary -->
                    <cart-checkout-summary v-bind:cart="cartArr" v-bind:promotions="promotions"
                    v-bind:show-status="this.showItemStatus"></cart-checkout-summary>
                    <!-- ก้อน Cart detail summary -->

                    <!-- ก้อน Cart total summary -->
                    <summary-price :on-submit="onSubmitForm" :submitting="submitting"
                                v-bind:summary="cartSummary"></summary-price>
                    <!-- ก้อน Cart total summary -->
                    </div>
                </div>
            </div>
        </div>


        <form id="continue-shipping-form" ref="continueShippingForm" method="post" v-bind:action="shippingFormUrl">
            <input type="hidden" name="submit_form_payment" value="1">
            <input type="hidden" name="reserve_id" v-bind:value="reserveId">
        </form>


        <!-- Unavailable product modal -->
        <modal
                v-bind:show="unavailableProduct"
                additional-custom-classes="address-modal-box"
                v-on:on-close-modal="onCloseUnavailableProductModal"
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
            </div>

            <div slot="footer" class="btn-wrap">
                <!--<button class="btn btn-close" type="button" v-on:click="continueShopping">{{ lang.continue_choose_product }}</button>-->
                <button class="btn btn-primary" v-on:click="continueProceedToShipping">{{ lang.confirm_to_proceed }}
                    <loading v-bind:show="submitting_confirmation_proceed"></loading>
                </button>
            </div>
        </modal>

    </div>

</template>

<script>
    import ProductAssortment from './product_type/Assortment'
    import ProductNonAssortmentInstallment from './product_type/NonAssortmentInstallment'
    import ProductNonAssortmentNoInstallment from './product_type/NonAssortmentNoInstallment'
    import CartSummary from '../detail/Summary'
    import CartCheckoutSummary from './summary/Summary'
    import 'jquery-validation'
    import Loading from '../../loading/Loading'
    import SummaryPrice from './SummaryPrice'
    import Modal from '../../bootstrap/Modal2'
    import ProductList from './product_type/list/List'
    import popup from '../../../libraries/popup'
    import {SET_CART_ITEMS, SET_OPEN_POPUP_ADDRESS, SET_RETURN_TO_CART_ON_CHANGE_ADDRESS} from '../../../store/mutation-types'
    import GoogleAnalytic from '../../../libraries/google_analytic'
    import GroupItems from '../detail/GroupItems'
    import AddressStore from '../../../libraries/store/store_address'
    import StoreShippingAddressContainer from '../checkout/shipping_address/store/Container'
    import { mapState, mapActions } from 'vuex'

    const googleAnanlytic = new GoogleAnalytic()
    const addressStore = new AddressStore()

    export default {
        name: 'cart-checkout-shipping',
        props: [
            'cart',
            'cartSummary',
            'currentStore',
            'promotions',
            'cartArr',
            'currentStore',
            'submitUrl',
            'memberAddress',
            'shippingAddresses',
            'shippingFormUrl',
            'deliveryDates',
            'showItemStatus',
            'configDate'
        ],
        components: {
            'product-assortment': ProductAssortment,
            'product-non-assortment-installment': ProductNonAssortmentInstallment,
            'product-non-assortment-no-installment': ProductNonAssortmentNoInstallment,
            'cart-summary': CartSummary,
            'cart-checkout-summary': CartCheckoutSummary,
            Loading,
            'summary-price': SummaryPrice,
            Modal,
            'product-list': ProductList,
            'group-items': GroupItems,
            StoreShippingAddressContainer
        },
        data: function () {
            return {
                validate_error: false,
                submitting: false,
                showExpiredModal: false,
                submittingReserveOrder: false,
                unavailableProduct: false,
                unavailableProductData: [],
                reserveId: null,
                isProductEmpty: false,
                showReserveSuccessModal: false,
                confirmation_proceed_error: false,
                submitting_confirmation_proceed: false
            }
        },
        computed: {
          ...mapState({
            selectedAddress: state => state.storeModule.selected_address
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
            },
            subDistrictId: function() {
                return _.get(this.selectedAddress, 'sub_districts.id', null)
            },
            addressId: function () {
                return _.get(this.selectedAddress, 'store_id', null)
            },
            addressType: function () {
                return _.get(this.selectedAddress, 'type', null)
            }
        },
        methods: {
          ...mapActions([
            'setSelectedAddress'
          ]),
            onAddDeliveryAddress: function () {

            },

            getForm: function () {
                return $(this.$refs.form)
            },

            getSubmitBtn: function () {
                return $(this.$refs.submitBtn)
            },

            disableSubmitBtn: function () {
                var btn = this.getSubmitBtn();
                btn.prop('disabled', true).attr('disabled', 'disabled');
            },

            enableSubmitBtn: function () {
                var btn = this.getSubmitBtn();
                btn.prop('disabled', false).removeAttr('disabled');
            },

            onSubmitComplete: function () {
                this.submitting = false;
                this.enableSubmitBtn();
            },

            onSubmitSuccess: function (response) {
                if (typeof response.data.status == 'string' && response.data.status == 'ok') {
                    let products = []
                    for (let i in this.cart) {
                        for (let j = 0; j < this.cart[i].items.length; ++j) {
                            let product = this.cart[i].items[j].content.data
                            product.qty = this.cart[i].items[j].quantity
                            product.price = this.cart[i].items[j].price.data.price
                            products.push(product)
                        }
                    }

                    googleAnanlytic.checkoutShipping(products, '', () => {
                        window.location = response.data.next_url;
                    })
                } else {
                    this.onSubmitError(response);
                }
            },

            onSubmitError: function (response) {
                var me = this
                this.onSubmitComplete();

                if ((typeof response.data.reserve_expired == 'number' || response.data.reserve_expired == 'string') && response.data.reserve_expired == 1) {
                    //this.showExpiredModal = true;
                    popup.open({
                        type: 'confirm',
                        message: this.lang.reserve_order_expire_title + '<br />' + this.lang.would_you_continue_checkout,
                        confirmText: this.lang.continue_checkout,
                        confirm: function (done) {
                            me.reReserveOrder(done)
                        }
                    })

                } else if ((typeof response.data.error_ma == 'number' || response.data.error_ma == 'string') && response.data.error_ma == 1) {
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
                } else if ((typeof response.data.payment_data_empty == 'number' || response.data.payment_data_empty == 'string') && response.data.payment_data_empty == 1) {
                    popup.open({
                        type: 'error',
                        message: this.lang.payment_data_must_not_empty,
                        confirmText: this.lang.ok,
                        confirm: function (done) {
                            me.goToPayment()
                        },
                        onClose: function () {
                            me.goToPayment()
                        }
                    })
                } else if ((typeof response.data.reserve_empty == 'number' || response.data.reserve_empty == 'string') && response.data.reserve_empty == 1) {
                    popup.open({
                        type: 'confirm',
                        message: this.lang.reserve_order_expire_title + '<br />' + this.lang.would_you_continue_checkout,
                        confirmText: this.lang.continue_checkout,
                        confirm: function (done) {
                            me.reReserveOrder(done)
                        }
                    })
                } else if (_.get(response.data, 'error_code') === 221001) {
                    me.$store.commit(SET_OPEN_POPUP_ADDRESS, true)
                    me.$store.commit(SET_RETURN_TO_CART_ON_CHANGE_ADDRESS, true)
                } else if (_.get(response.data, 'delivery_store_address_is_changed') === 1) {
                  popup.open({
                    type: 'confirm',
                    message: this.trans('confirm_change_delivery_address_message'),
                    confirm() {
                      me.updateDeliveryStoreAddress(_.get(response.data, 'address'))
                    }
                  })
                } else {
                    var message = '';
                    if (typeof response.data.message == 'string') {
                        message = response.data.message;
                    }

                    if (response.data.status === "error_connect") {
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
                    }else{
                      popup.open({
                        title: this.lang.proceeding_ordering_not_successful,
                        message,
                        type: 'error',
                        center: true,
                        confirm: () => {
                          switch (_.get(response, 'data.action')) {
                            case 'close_popup':
                              popup.close()
                              callback()
                              break;
                            case 'redirect':
                              window.location = _.get(response, 'data.action_target', me.$store.getters.locale_url)
                              break;
                            default:
                              window.location = me.$store.getters.locale_url
                          }

                        }
                      })
                    }
                }
            },
            onCloseExpiredModal: function () {
                this.showExpiredModal = false;
                this.submittingReserveOrder = false;
            },
            cancelCreateOrder: function () {
                window.location = this.$store.getters.locale_url;
            },
            reReserveOrder: function (done, delayOnError) {
                var me = this
                this.submittingReserveOrder = true;
                axios.post(this.$store.getters.locale_url + '/carts/reserve-order')
                    .then(function (response) {
                        done()
                        popup.close()
                        me.onSubmitReserveOrderSuccess(response, delayOnError)
                    })
                    .catch(function (response) {
                        done()
                        me.onSubmitReserveOrderError(response, delayOnError)
                    })
            },


            onSubmitReserveOrderSuccess: function (response, delayOnError) {
                this.showExpiredModal = false;
                var me = this;
                if (response.data.status == 'ok') {
                    this.reserveId = response.data.reserve_id;
                    this.showReserveSuccessModal = true;
                    // popup.open({
                    //     type: 'success',
                    //     message: this.lang.reserve_order_success,
                    //     confirmText: this.lang.continue_checkout_and_reload,
                    //     confirm: function () {
                    //         window.location.reload()
                    //     },
                    //     onClose: function () {
                    //         window.location.reload()
                    //     }
                    // })
                    window.location.reload()
                } else {
                    //Error
                    this.onSubmitReserveOrderError(response, delayOnError);
                }
            },

            onSubmitReserveOrderError: function (response, delayOnError) {
                console.log('delayOnError', delayOnError)
                this.onSubmitReserveOrderComplete();
                this.showExpiredModal = false;
                this.reserveId = null;
                this.submittingReserveOrder = false;
                this.submitting_confirmation_proceed = false
                this.unavailableProduct = false

                if (delayOnError) {
                    setTimeout(() => {

                        this.handleReserveOrderError(response)
                    }, 600)
                } else {
                    this.handleReserveOrderError(response)
                }

            },

            handleReserveOrderError(response) {
                if (response.data
                    && typeof response.data.has_unavailable_product == 'number'
                    && response.data.has_unavailable_product == 1) {
                    console.log('has_unavailable_product')
                    this.unavailableProduct = true;
                    var cartData = this.getCartData(response.data.unavailable_products);
                    this.unavailableProductData = cartData;
                    this.checkReserveProductEmpty(response);
                    this.reserveId = response.data.reserve_id;
                } else if (typeof response.data == 'object' && typeof response.data.message == 'string') {
                    popup.open(this.lang.proceeding_ordering_not_successful, response.data.message, 'error', true)
                } else {
                    popup.open(this.lang.proceeding_ordering_not_successful, this.lang.please_try_again, 'error', true)
                }
            },

            onSubmitReserveOrderComplete: function () {

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
            onCloseUnavailableProductModal: function () {
                this.unavailableProduct = false;
            },
            continueShopping: function () {
                window.location = this.$store.getters.locale_url;
            },
            continueProceedToShipping: function () {
                // var me = this;
                // setTimeout(function () {
                //     $(me.$refs.continueShippingForm).trigger('submit');
                // }, 10)

                this.manageCartItems()
            },
            onCloseReserveSuccessModal: function () {
                this.showReserveSuccessModal = false;
                window.location.reload();
            },
            onReReserveOrderSuccess: function () {
                window.location.reload();
            },
            goToPayment: function () {
                window.location.href = this.$store.getters.locale_url + '/carts/payment';
            },
            manageCartItems() {
                this.confirmation_proceed_error = false
                let updateItems = []
                let removeItems = []
                for (let i = 0; i < this.unavailableProductData.length; ++i) {
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
                                this.reReserveOrder(() => {
                                }, true)
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
            onSubmitForm() {
                if (!this.submitting) {
                    $(this.$refs.form).trigger('submit')
                }
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
          formatData: function (datas) {
            let newData = {};
            for (let i = 0; i < datas.length; ++i) {
              newData[datas[i].name] = datas[i].value;
            }

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
              "district":{
                "id": _.get(memberAddress, 'original_district.id'),
                "name": _.get(memberAddress, 'district'),
                "original_name":{
                  "th": _.get(memberAddress, 'original_district.th'),
                  "en": _.get(memberAddress, 'original_district.en')
                }
              },
              "province":{
                "id": _.get(memberAddress, 'original_province.id'),
                "name": _.get(memberAddress, 'province'),
                "original_name":{
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
            this.setSelectedAddress(newSelectedAddress)
          },
        },
        mounted: function () {
            this.$store.commit(SET_CART_ITEMS, this.cartArr);

            var me = this;
            var form = this.getForm();
            var submitBtn = this.getSubmitBtn();
            form.validate({
                rules: {
                    'shipping_address_id': {
                        required: function () {
                            if ($('#other-address').prop('checked')) {
                                return true;
                            }

                            return false;
                        }
                    }
                },
                messages: {
                    'product_non_assortment_with_installment[delivery_method]': {
                        required: this.lang.please_choose_shipping_address_type
                    },
                    'shipping_address_id': {
                        required: this.lang.please_choose_shipping_address
                    }
                },
                submitHandler: function (e) {
                    me.validate_error = false;

                    //Check current address id is same changed address
                    console.log(_.get(me.$store.state.storeModule.selected_address, 'id'))
                    console.log(_.get(addressStore.getSelected(), 'id'))
                    if (_.get(me.$store.state.storeModule.selected_address, 'id') !== _.get(addressStore.getSelected(), 'id')) {
                        popup.open({
                            message: me.trans('create_order_shipping_address_not_same_current_shipping_address'),
                            showLoading: true,
                            confirm() {
                                window.location = me.$store.getters.locale_url + '/carts'
                            }
                        })
                    } else {
                        me.submitting = true;
                        me.disableSubmitBtn();
                        axios.post(me.submitUrl, form.serialize())
                            .then(me.onSubmitSuccess)
                            .catch(me.onSubmitError);

                    }

                    return false;
                },
                invalidHandler: function (result) {
                    me.validate_error = true;
                    return false;
                }
            });

            $(this.$refs.submitBtn).removeAttr('disabled')
        }
    }
</script>