<template>
    <div>
        <modal
                v-bind:show="show"
                v-on:on-close-modal="onModalClose"
                v-bind:show-header="false"
                v-bind:reverse-button-align="true"
                v-bind:cancel-text="lang.continue_shopping_again"
                v-bind:ok-text="lang.add_to_cart_success_checkout"
                v-on:on-ok="goToCart"
                v-bind:center="false"
                :show-ok="false"
        >

            <div>
                <div class="text-center">
                    <p class="item-price" id="lbl_pop_up_alert_message">
                       {{ errorText }}
                    </p>
                </div>
                <div class="row">
                    <div class="col-xs-8 col-xs-offset-2 col-sm-8 col-sm-offset-2">
                        <div class="product">
                            <div class="row">
                                <div class="no-padding col-xs-4 col-sm-4">
                                    <img v-bind:src="image" class="img-responsive">
                                </div>
                                <div class="cart-text-detail col-xs-8 col-sm-8">
                                    <b>{{ title }}</b><br>
                                    <b>{{ lang.product_code }} {{ id }}</b><br />
                                    <b><span class="item-price">{{ price | money }} ฿ </span></b> <!--{{ selling_uom}}--><br>
                                    <s v-if="price < normal_price">{{ normal_price | money }} ฿</s>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </modal>
    </div>
</template>


<script>
    import Modal from '../bootstrap/Modal2'
    import { SET_ADD_TO_CART_ERROR_LOW_STOCK } from '../../store/mutation-types'
    import StringHelper from '../../libraries/StringHelper'
    import accounting from 'accounting'

    export default {
        name: 'add-to-cart-error-low-stock',
        components: {
            Modal
        },
        data: function () {
            return {
                url: this.$store.getters.url + '/',
                locale_url: this.$store.getters.locale_url + '/'
            }
        },
        methods: {
            onModalClose: function () {
                this.$store.commit(SET_ADD_TO_CART_ERROR_LOW_STOCK, false)

                if (this.$store.state.cartModule.addToCartSuccessFromSelectStore === true) {
                    window.location.reload();
                }
            },
            getDataValue: function (fieldName) {
                var data = this.$store.getters.addToCartData;
                if (data) {
                    if (typeof data[fieldName] == 'string' || typeof data[fieldName] == 'number') {
                        return data[fieldName];
                    }
                }

                return '';
            },
            goToCart: function () {
                window.location = this.locale_url + 'carts';
            }
        },
        computed: {
            show: function() {
                return this.$store.state.cartModule.addToCartErrorLowStock;
            },
            title: function() {
                var name = this.getDataValue('name');
                if (typeof name == 'string' && name != '') {
                    return StringHelper.htmlspecialcharDecode(name);
                }

                var title = this.getDataValue('title');
                return StringHelper.htmlspecialcharDecode(title);
            },
            normal_price: function() {
                return this.getDataValue('normal_price');
            },
            price: function () {
                return this.getDataValue('price');
            },
            image: function () {
                if (this.$store.getters.addToCartData) {
                    var image = this.$store.getters.addToCartData.thumbnail;

                    if ((typeof image == 'object' || typeof image == 'array') && image.length > 0) {
                        return image[0];
                    } else if (typeof image == 'string') {
                        return image;
                    }
                }

                return '';
            },
            selling_uom: function () {
                return this.getDataValue('selling_uom');
            },
            quantity: function () {
                return this.getDataValue('quantity');
            },
            lang: function () {
                return this.$store.getters.lang
            },
            id: function() {
                return this.getDataValue('id');
            },
            errorText() {
                let text = ''
                switch (this.$store.state.cartModule.errorType) {
                    case 'out_of_stock':
                        text = this.$store.getters.lang.product_out_of_stock_error
                        break
                    case 'over_stock':
                        text = this.$store.getters.lang.product_over_stock_error
                        let quantity = parseInt(_.get(this.$store.state.cartModule.errorData, 'errors.available', 0))
                        quantity = accounting.formatNumber(quantity, 0)
                        text = text.replace(/:quantity/, quantity)
                        break
                }

                return text
            }
        }
    }
</script>