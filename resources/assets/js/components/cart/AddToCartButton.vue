<template>
    <span>
        <!-- ปุ่มที่เป็น product list -->
        <a v-if="!bigAddToCartButton" href="javascript:;" class="btn-add-to-cart" v-on:click.prevent="addToCart(1)">
            <div class="box-bg-icon2 pull-right">
                <div class="box-icon-cart-wishlist">
                    <img v-bind:src="iconCartImage" width="25px" v-show="!adding"/>
                    <loading v-bind:show="adding"></loading>
                </div>
            </div>
        </a>

        <!-- ปุ่มหน้า product detail แบบมีสินค้าในสาขา -->
        <a v-if="bigAddToCartButton" :class="{'btn-add-to-cart': !disabledButton, 'btn btn-disabled': disabledButton}"
           href="javascript:;" :disabled="disabledButton" v-on:click.prevent="addToCart(productDetailQuantity)">
            <!-- มีสินค้าใน store -->
            <!-- <button type="button" v-bind:class="{'box-bg-icon-cw btn-disabled':disabledButton, 'btn-add-to-cart':!disabledButton}" :disabled="disabledButton">
            </button> -->
            <img v-bind:src="iconCartImageWhite" width="25px" v-show="!adding"/>
            <loading v-bind:show="adding"></loading>
            {{ getLang('add_to_cart') }}
        </a>


        <!-- ปุ่มหน้า product detail แบบไม่มีสินค้าในสาขา -->
        <a v-if="bigAddToCartButton && false" href="javascript:;" v-on:click.prevent="pickStore()">
            <!-- ไม่มีสินค้าใน store -->
            <button type="button" class="btn-add-to-cart" v-if="!productObj.in_store">
                {{ getLang('add_to_cart_with_no_product_not_in_store') }}
            </button>
        </a>
    </span>
</template>

<script>
    import {
        CHANGE_SHOW_PICKUP_STORE,
        SET_SHOW_PICKUP_STORE_CALLBACK,
        SET_ADD_TO_CART_SUCCESS_FROM_STORE,
        SET_SHOW_PICKER_PRODUCT_NOT_IN_STORE,
        SET_AVAILABLE_STORES
    } from '../../store/mutation-types'
    import popup from '../../libraries/popup'
    import Cart from '../../libraries/cart'
    import {trans} from "../../libraries/trans"

    const cartObj = new Cart()

    export default {
        name: 'add-to-cart-button',
        data: function () {
            return {
                added: false,
                iconCartImage: this.$store.getters.url + '/assets/images/icon-Cart.png',
                iconCartImageWhite: this.$store.getters.url + '/assets/images/icon-Cart-white.png',
                adding: false,
                productObj: {},
                afterSelectStore: false,
                quantity: 1
            };

        },
        props: {
            button_type: {
                type: Number,
                default: 1 // 1 = home page, 2 = promotion page, 3 = detail page
            },
            itemClass: {
                type: String,
                default: 'box-product'
            },
            product: {
                type: String,
                required: true
            },
            disabledButton: {
                type: Boolean,
                default: false
            }
        },
        methods: {
            addToCart: function (quantity) {
                if (!this.disabledButton) {
                    this.quantity = quantity
                    var me = this;

                    if (!this.$store.getters.currentStore) {
                        this.afterSelectStore = true;
                        this.$store.commit(SET_AVAILABLE_STORES, null)
                        this.$store.commit(SET_SHOW_PICKUP_STORE_CALLBACK, function () {
                            if (me.afterSelectStore) {
                                me.submitAddToCart(quantity)
                            } else {
                                if (me.productObj.in_store) {
                                    me.submitAddToCart(quantity)
                                } else {
                                    let message = trans('product_not_available_in_all_stores')
                                    if (_.get(me.productObj, 'in_store_error_code')) {
                                        let code = _.get(me.productObj, 'in_store_error_code')
                                        message = trans('product_not_available_in_all_stores_with_code', {code: code})
                                    }
                                    popup.open(me.$store.getters.lang.could_not_add_product_to_cart, message, 'error');
                                }
                            }
                        });


                        console.log('CHANGE_SHOW_PICKUP_STORE')
                        this.$store.commit(CHANGE_SHOW_PICKUP_STORE, true)
                    } else {
                        if (me.productObj.in_store) {
                            me.submitAddToCart(quantity)
                        } else {
                            let message = trans('product_not_available_in_all_stores')
                            if (_.get(me.productObj, 'in_store_error_code')) {
                                let code = _.get(me.productObj, 'in_store_error_code')
                                message = trans('product_not_available_in_all_stores_with_code', {code: code})
                            }
                            popup.open(this.$store.getters.lang.could_not_add_product_to_cart, message, 'error');
                            //cartObj.addToCartError({}, this.$store.commit, this.$store.getters, {product: {}})
                            //me.productNotInStore();
                        }
                    }
                }
            },

            submitAddToCart: function (quantity) {
                this.adding = true;
                var me = this;

                this.$store.commit(SET_ADD_TO_CART_SUCCESS_FROM_STORE, this.afterSelectStore)
                this.$store.dispatch('addToCart', {
                    'product': this.productObj,
                    'qty': quantity,
                    'callback': function () {
                        me.adding = false;
                    }
                });
            },

            productNotInStore: function () {
                var me = this
                if (this.productObj.available_store
                    && (
                        typeof this.productObj.available_store == 'object'
                        || typeof this.productObj.available_store == 'array'
                    )
                    && this.productObj.available_store.length > 0
                ) {
                    this.afterSelectStore = true;
                    this.$store.commit(SET_AVAILABLE_STORES, this.productObj.available_store)
                    this.$store.commit(SET_SHOW_PICKUP_STORE_CALLBACK, function () {
                        if (me.afterSelectStore) {
                            me.submitAddToCart(me.quantity)
                        } else {
                            if (me.productObj.in_store) {
                                me.submitAddToCart(quantity)
                            } else {
                                me.productNotInStore();
                            }
                        }
                    });
                    this.$store.commit(CHANGE_SHOW_PICKUP_STORE, true)
                } else {
                    let message = trans('product_not_available_in_all_stores')
                    if (_.get(this.productObj, 'in_store_error_code')) {
                        let code = _.get(this.productObj, 'in_store_error_code')
                        message = trans('product_not_available_in_all_stores_with_code', {code: code})
                    }
                    popup.open({
                        title: this.getLang('could_not_add_product_to_cart'),
                        message: message,
                        type: 'info',
                        confirmText: this.getLang('back_to_home'),
                        confirm: function () {
                            window.location.href = me.$store.getters.locale_url + '/';
                        }
                    })
                }
            },

            getLang: function (key) {
                return this.$store.getters.lang[key];
            },
            pickStore: function () {
                this.$store.commit(SET_SHOW_PICKER_PRODUCT_NOT_IN_STORE, true)
            }
        },
        computed: {
            buttonType1: function () {
                return this.button_type == 1
            },
            buttonType2: function () {
                return this.button_type == 2
            },
            bigAddToCartButton: function () {
                return this.button_type == 3
            },
            productDetailQuantity: function () {
                return this.$store.state.ProductDetail.quantity
            }
        },

        mounted: function () {
            this.productObj = JSON.parse(this.product)
        }
    }
</script>