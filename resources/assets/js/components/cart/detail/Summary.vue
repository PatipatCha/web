<template>
    <div style="margin-top: 20px;">
        <div class="row">
            <div class="col-md-6">
                <h3 class="title text-bold">{{ lang.delivery_fee_title }}</h3>
                <ul class="cart-rate">
                    <li v-for="(rate, index) in shippingRates" :key="index">{{ rate }}</li>
                </ul>
                <!--<div><strong>เงื่อนไขการจัดส่ง</strong></div>-->
            </div>
            <div class="col-md-5 col-md-offset-1">
                <div class="summary-section">
                    <div class="total-price-box">
                        <div class="total-price1"><b>{{ lang.shopping_total }}</b></div>
                        <div class="total-price2" id="lbl_shopping_total">
                            {{ sub_total | money }} ฿
                        </div>

                        <div class="total-discount" v-if="discount > 0">
                            <div class="total-price1"><b><span style="color: rgb(240, 22, 22);">{{ lang.total_discount }}</span></b></div>
                            <div class="total-price2" id="lbl_total_discount">
                                <span style="color: rgb(240, 22, 22);">{{ discount | money }} ฿</span>
                            </div>
                        </div>

                        <div class="total-price1"><b>{{ lang.subtotal_in_summary }}</b></div>
                        <div class="total-price2" id="lbl_subtotal">
                            {{ sub_total_include_vat | money }} ฿
                        </div>

                        <div class="total-price1"><b>{{ lang.delivery_fee }}</b></div>
                        <div class="total-price2" id="lbl_delivery_fee">
                            {{ getDeliveryFee }}
                        </div>

                        <!--<div class="clearfix"></div>-->
                        <!--<div class="total-price1"><b>Est. Sale Tax</b></div>-->
                        <!--<div class="total-price2">-->
                        <!--{{ sale_tax | money }} ฿-->
                        <!--</div>-->
                        <!--<div class="clearfix"></div>-->
                        <!--<div class="total-price1"><b>Shipping Fee</b></div>-->
                        <!--<div class="total-price2">-->
                        <!--{{ delivery_fee | money }} ฿-->
                        <!--</div>-->
                        <!--<div class="clearfix"></div>-->
                        <!--<div class="total-price1"><b>Product Fee</b></div>-->
                        <!--<div class="total-price2">-->
                        <!--{{ product_fee | money }} ฿-->
                        <!--</div>-->
                        <div class="clearfix"></div>
                    </div>

                    <div class="total-price-box2">
                        <div class="total-price1"><b>{{ lang.grand_total }}</b></div>
                        <div class="total-price2"><b>{{ grand_total | money }} ฿</b></div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { money } from '../../../filters/accounting'

    export default{
        name: 'cart-summary',
        props: {
            shippingRates: {
                type: Array
            }
        },
        computed: {
            sub_total: function () {
                return this.$store.state.cartModule.summary.sub_total
            },
          sub_total_include_vat: function () {
            return parseFloat(this.$store.state.cartModule.summary.sub_total) - parseFloat(this.$store.state.cartModule.summary.discount)
          },
            sale_tax: function () {
                return this.$store.state.cartModule.summary.sale_tax
            },
            delivery_fee: function () {
                return this.$store.state.cartModule.summary.delivery_fee
            },
            product_fee: function () {
                return this.$store.state.cartModule.summary.product_fee
            },
            grand_total: function () {
                return this.$store.state.cartModule.summary.grand_total
            },
            isEmpty: function () {
                return this.$store.getters.cartCount < 1 ? true : false
            },
            delivery_fee: function () {
                return this.$store.state.cartModule.summary.delivery_fee
            },
            discount: function () {
                return this.$store.state.cartModule.summary.discount
            },
            lang: function () {
                return this.$store.getters.lang;
            },
          getDeliveryFee() {
            if (isNaN(parseFloat(this.delivery_fee)) || parseFloat(this.delivery_fee) < 1) {
                return this.lang.free
            }

            return money(this.delivery_fee) + ' ฿'
          }
        }
    }
</script>