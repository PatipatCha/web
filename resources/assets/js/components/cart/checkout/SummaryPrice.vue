<template>
    <div>
        <div class="summary-section">
            <div class="total-price-box">
                <div class="total-price1"><b>{{ lang.shopping_total }}</b></div>
                <div class="total-price2" id="lbl_shopping_total">
                    {{ sub_total | money }} ฿
                </div>

                <div v-if="discount > 0">
                    <div class="clearfix"></div>
                    <div class="total-price1"><b><span style="color: rgb(240, 22, 22);">{{ lang.total_discount }}</span></b>
                    </div>
                    <div class="total-price2" id="lbl_total_discount">
                        <span style="color: rgb(240, 22, 22);">{{ discount | money }} ฿</span>
                    </div>
                </div>

                <div class="total-price1"><b>{{ lang.subtotal_in_summary }}</b></div>
                <div class="total-price2" id="lbl_subtotal">
                    {{ sub_total_include_vat | money }} ฿
                </div>
                <!-- <div class="clearfix"></div>-->
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
                <!--</div> -->

                <div>
                    <div class="clearfix"></div>
                    <div class="total-price1"><b>{{ lang.delivery_fee }}</b></div>
                    <div class="total-price2" id="lbl_delivery_fee">
                        {{ getDeliveryFee }}
                    </div>
                </div>

                <div class="clearfix"></div>
            </div>

            <div class="total-price-box2">
                <div class="total-price1"><b><span>{{ lang.grand_total }}</span></b></div>
                <div class="total-price2"><b><span>{{ grand_total | money }} ฿</span></b></div>
                <div class="clearfix"></div>
            </div>

            <!--<div class="total-price-box2" v-if="refund_amount > 0 || return_discount_total > 0 || delivery_fee_cancel > 0">-->
                <div class="total-price-box2" v-if="total_refund_amount > 0">
                    <div class="total-price1"><b><span style="color: rgb(240, 22, 22);">{{ lang.refund_amount }}</span></b>
                    </div>
                    <div class="total-price2">
                        <b>
                            <span style="color: rgb(240, 22, 22);">{{ total_refund_amount | money }} ฿</span>
                        </b>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="total-price-box2" v-if="refund_discount_total > 0">
                    <div class="total-price1"><b><span
                            style="color: rgb(240, 22, 22);">{{ lang.refund_discount_total }}</span></b></div>
                    <div class="total-price2"><b><span style="color: rgb(240, 22, 22);">{{ refund_discount_total | money }} ฿</span></b>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="total-price-box2" v-if="return_discount_total > 0">
                    <div class="total-price1"><b><span style="color: rgb(240, 22, 22);">{{ lang.return_discount_total }}</span></b>
                    </div>
                    <div class="total-price2"><b><span style="color: rgb(240, 22, 22);">{{ return_discount_total | money }} ฿</span></b>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="total-price-box2" v-if="delivery_fee_cancel > 0">
                    <div class="total-price1"><b><span
                            style="color: rgb(240, 22, 22);">{{ lang.delivery_fee_cancel }}</span></b>
                    </div>
                    <div class="total-price2"><b><span
                            style="color: rgb(240, 22, 22);">{{ delivery_fee_cancel | money }} ฿</span></b>
                    </div>
                    <div class="clearfix"></div>
                </div>
            <!--</div>-->

            <div v-if="total_refund_discount_amount > 0 && (refund_amount > 0 || return_discount_total > 0 || delivery_fee_cancel > 0)" class="total-price-box2">
                <div class="total-price1"><b><span>{{ lang.total_refund_discount_amount }}</span></b></div>
                <div class="total-price2"><b><span> {{ total_refund_discount_amount | money }} ฿</span></b></div>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>

            <div class="row" v-if="!hideBtn">
                <div class="col-sm-6 col-md-12 col-lg-6 margin-bottom-15">
                    <a :href="backUrl" class="btn btn-default btn-block">
                        <!-- <i class="far fa-arrow-left"></i> -->
                        {{ trans('shopping_cart') }}
                    </a>
                </div>                
                <div class="col-sm-6 col-md-12 col-lg-6">
                    <a class="btn btn-primary btn-block pull-right"  @click="onSubmit" :disabled="submitting">
                        <!-- <i class="far fa-arrow-right"></i> -->
                       <loader :show="submitting"></loader> {{ trans('continue_checkout') }}
                    </a>
                </div>                
            </div>
            
        </div>
    </div>
</template>

<script>
    import {SET_CART_SUMMARY} from '../../../store/mutation-types'
    import {money} from '../../../filters/accounting'
    import Loader from '../../loading/Loading'

    export default {
        name: 'cart-summary',
        props: ['summary', 'submitting', 'onSubmit', 'hideBtn'],
        components: {
            Loader
        },
        computed: {
            sub_total: function () {
                if (typeof this.summary.sub_total == 'string' || typeof this.summary.sub_total == 'number') {
                    return this.summary.sub_total;
                }
                return 0;
            },
            sub_total_include_vat: function () {
                return parseFloat(this.sub_total) - parseFloat(this.discount)
            },
            discount: function () {
                if (typeof this.summary.discount == 'string' || typeof this.summary.discount == 'number') {
                    if (parseFloat(this.summary.discount) > 0) {
                        return parseFloat(this.summary.discount);
                    }
                }
                return 0;
            },
            refund_amount: function () {
                if (typeof this.summary.refund != 'undefined') {
                    if (typeof this.summary.refund.amount == 'string' || typeof this.summary.refund.amount == 'number') {
                        if (parseFloat(this.summary.refund.amount) > 0) {
                            return parseFloat(this.summary.refund.amount);
                        }
                    }
                }
                return 0;
            },
            refund_discount_total: function () {
                if (typeof this.summary.refund != 'undefined') {
                    if (typeof this.summary.refund.amount == 'string' || typeof this.summary.refund.amount == 'number') {
                        if (parseFloat(this.summary.refund.discount.total) > 0) {
                            return parseFloat(this.summary.refund.discount.total);
                        }
                    }
                }
                return 0;
            },
            refund_discount_amount: function () {
                if (typeof this.summary.refund != 'undefined') {
                    if (typeof this.summary.refund.amount == 'string' || typeof this.summary.refund.amount == 'number') {
                        if (parseFloat(this.summary.refund.amount) > 0 && parseFloat(this.summary.refund.discount.total) > 0) {
                            return parseFloat(this.summary.refund.amount) - parseFloat(this.summary.refund.discount.total);
                        } else {
                            return parseFloat(this.summary.refund.amount)
                        }
                    }
                }
                return 0;
            },
            total_refund_discount_amount: function () {
                if (typeof this.summary.refund != 'undefined') {
                    if (typeof this.summary.refund.amount == 'string' || typeof this.summary.refund.amount == 'number') {
                        if (parseFloat(this.summary.refund.amount) > 0 && parseFloat(this.summary.refund.discount.total) > 0) {
                            return parseFloat(this.grand_total) - parseFloat(this.summary.refund.amount) + parseFloat(this.summary.refund.discount.total) - this.delivery_fee_cancel;
                        } else {
                            return parseFloat(this.grand_total) - parseFloat(this.summary.refund.amount) - this.delivery_fee_cancel
                        }
                    }
                }
                return 0;
            },
            return_amount: function () {
                if (typeof this.summary.return != 'undefined') {
                    if (typeof this.summary.return.amount == 'string' || typeof this.summary.return.amount == 'number') {
                        if (parseFloat(this.summary.return.amount) > 0) {
                            return parseFloat(this.summary.return.amount);
                        }
                    }
                }
                return 0;
            },
            return_discount_total: function () {
                if (typeof this.summary.return != 'undefined') {
                    if (typeof this.summary.return.amount == 'string' || typeof this.summary.return.amount == 'number') {
                        if (parseFloat(this.summary.return.discount.total) > 0) {
                            return parseFloat(this.summary.return.discount.total);
                        }
                    }
                }
                return 0;
            },
            return_discount_amount: function () {
                if (typeof this.summary.return != 'undefined') {
                    if (typeof this.summary.return.amount == 'string' || typeof this.summary.return.amount == 'number') {
                        if (parseFloat(this.summary.return.amount) > 0 && parseFloat(this.summary.return.discount.total) > 0) {
                            return parseFloat(this.summary.return.amount) - parseFloat(this.summary.return.discount.total);
                        } else {
                            return parseFloat(this.summary.return.amount)
                        }
                    }
                }
                return 0;
            },
            total_return_discount_amount: function () {
                if (typeof this.summary.return != 'undefined') {
                    if (typeof this.summary.return.amount == 'string' || typeof this.summary.return.amount == 'number') {
                        if (parseFloat(this.summary.return.amount) > 0 && parseFloat(this.summary.return.discount.total) > 0) {
                            return parseFloat(this.grand_total) - parseFloat(this.summary.return.amount) + parseFloat(this.summary.return.discount.total);
                        } else {
                            return parseFloat(this.grand_total) - parseFloat(this.summary.return.amount)
                        }
                    }
                }
                return 0;
            },
            total_refund_amount: function () {
                return this.refund_amount
            },
            sale_tax: function () {
                return this.$store.state.cartModule.summary.sale_tax
            },
            delivery_fee: function () {
                return this.summary.delivery_fee
            },
            product_fee: function () {
                return this.$store.state.cartModule.summary.product_fee
            },
            grand_total: function () {
                if (typeof this.summary.grand_total == 'string' || typeof this.summary.grand_total == 'number') {
                    return parseFloat(this.summary.grand_total);
                }
                return 0;
            },

            delivery_fee_cancel: function () {
                if (typeof this.summary.delivery_fee_cancel == 'string' || typeof this.summary.delivery_fee_cancel == 'number') {
                    return parseFloat(this.summary.delivery_fee_cancel);
                }
                return 0;
            },
            isEmpty: function () {
                return this.$store.getters.cartCount < 1 ? true : false
            },
            lang: function () {
                return this.$store.getters.lang;
            },
            getDeliveryFee() {
                if (isNaN(parseFloat(this.delivery_fee)) || parseFloat(this.delivery_fee) < 1) {
                    return this.lang.free
                }

                return money(this.delivery_fee) + ' ฿'
            },
            backUrl() {
                return this.$store.getters.locale_url+'/carts'
            }
        }
    }
</script>