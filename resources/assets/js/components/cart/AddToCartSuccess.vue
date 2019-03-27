<template>
    <div>
        <modal
                v-bind:show="show"
                v-on:on-close-modal="onModalClose"
                v-on:on-before-open-modal="onModalOpen"
                v-bind:show-header="false"
                v-bind:reverse-button-align="true"
                v-bind:cancel-text="lang.continue_shopping_again"
                v-bind:ok-text="lang.add_to_cart_success_checkout"
                v-on:on-ok="goToCart"
                v-bind:center="false"
        >

            <div>
                <div class="text-center">
                    <p class="text-success">
                        <img :src="url+'assets/images/icon-cheak-20px.png'" alt="">
                        <span id="lbl_pop_up_alert_message">{{ successText }}</span>
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
    import { SET_ADD_TO_CART_SUCCESS } from '../../store/mutation-types'
    import StringHelper from '../../libraries/StringHelper'
    import pluralize from 'pluralize'
    import { number_comma } from '../../filters/accounting'
    import { mapGetters } from 'vuex'

    export default {
        name: 'add-to-cart-success',
        components: {
            Modal
        },
        data: function () {
            return {
                url: this.$store.getters.url + '/',
                locale_url: this.$store.getters.locale_url + '/',
              myQuantity: 0
            }
        },
        methods: {
            onModalClose: function () {
                this.$store.commit(SET_ADD_TO_CART_SUCCESS, {'success': false, 'data': null})

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
            },
          onModalOpen() {
              this.myQuantity = this.$store.state.cartModule.addToCartData ? this.$store.state.cartModule.addToCartData.quantity : 0
          }
        },
        computed: {
          ...mapGetters([
            'addToCartQuantity'
          ]),
            show: function() {
                return this.$store.getters.addToCartSuccess;
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
            itemText() {
                if (GLOBAL_SETTING.current_locale == 'th') {
                    return this.$store.getters.lang.piece
                }
                return pluralize(this.$store.getters.lang.item_singular, this.getDataValue('quantity'))
            },
            successText() {
              return this.lang.added_product_to_cart.replace(/:quantity/, number_comma(this.myQuantity)).replace(/:item/, this.itemText)
            }
        }
    }
</script>