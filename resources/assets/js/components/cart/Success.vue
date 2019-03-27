<template>
    <div>
        <cart-checkout-summary
                v-bind:cart="cart"
                v-bind:promotions="promotions"
                position="bottom"
                :estimated-dates="estimatedDates"
                :show-status="displayStatus"
                :calculate-date="calculateDate"
                :config-delivery-date="configDate"
                :order-date="orderDate"
                :delivery-type="deliveryType"
        >
        </cart-checkout-summary>
        <div class="row">
            <div class="col-sm-6">
                <div v-if="deliveryType == 'shipping'" v-html="remark"></div>
            </div>
            <div class="col-sm-6 col-sm-offset-0 col-md-4 col-md-offset-2">
                <cart-detail-summary :hide-btn="true" v-bind:summary="cartSummary"></cart-detail-summary>
            </div>
        </div>
    </div>
</template>

<script>
    import CartCheckoutSummary from './checkout/summary/Summary'
    import Summary from './checkout/SummaryPrice'

    export default {
        name: 'cart-success',
        components: {
            CartCheckoutSummary,
            'cart-detail-summary': Summary
        },
        props: [
            'cart',
            'cartSummary',
            'promotions',
            'clearCart',
            'estimatedDates',
            'showStatus',
            'calculateDate',
            'configDeliveryDate',
            'orderDate',
            'deliveryType'
        ],
        mounted: function () {
            if (this.clearCart == 1) {
                console.log('clear cart')
                this.$store.dispatch('clearCart')
            } else {
                console.log('Not clear cart')
            }

        },
        computed: {
            remark() {
                return this.$store.getters.lang.delivery_remark
            },
            displayStatus() {
                return !_.isEmpty(this.showStatus)
            },
            configDate() {
                return Object.assign({},this.configDeliveryDate);
            }
        }
    }
</script>