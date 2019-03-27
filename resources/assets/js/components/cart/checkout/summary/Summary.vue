<template>
    <div class="summary-box">
        <div class="pay-pickup-text text-bold">
            <b>{{ lang.your_order }}</b>
        </div>

        <div class="promotion" v-if="position == 'top'">
            <promotion v-bind:promotions="promotions"></promotion>
        </div>

		<div v-if="showStatus" class="cart-pd-table clearfix thead">
            <div class="col-xs-6">{{ lang.product_details }}</div>
            <div class="col-xs-1 no-padding text-center hidden-xs">{{ lang.qty }}</div>
            <div class="col-xs-2 no-padding text-center hidden-xs">{{ lang.subtotal }}</div>
            <div class="col-xs-3 text-center no-padding hidden-xs">{{ lang.step4_status }}</div>
        </div>
        <div v-else class="cart-pd-table clearfix thead">
            <div class="col-xs-7">{{ lang.product_details }}</div>
            <div class="col-xs-2 no-padding text-center hidden-xs">{{ lang.qty }}</div>
            <div class="col-xs-3 no-padding text-center hidden-xs">{{ lang.subtotal }}</div>
        </div>
        <div class="summary-box-item">
        	<div class="scroll">
				<group
                        v-bind:show-status="showStatus"
                        v-bind:item="item"
                        v-for="item in cart"
                        :key="item.id"
                        :estimated-dates="estimatedDates"
                        :config-date="configDate"
                        :calculate-date="calculateDate"
                        :config-delivery-date="configDeliveryDate"
                        :order-date="orderDate"
                        :delivery-type="deliveryType"
                >
                </group>
	        </div>
        </div>

        <div class="promotion" v-if="position == 'bottom'">
            <promotion v-bind:promotions="promotions" :show-title="true"></promotion>
        </div>
    </div>
</template>
<script>
	import Promotion from './Promotion'
	import Group from './Group'

    export default {
        name: 'cart-checkout-summary',
		components: {
            Promotion,
            Group
		},
		props: {
            cart: {
                type: Array
            },
            promotions: {
                type: Array
            },
            position: {
                type: String,
                default: 'top'
            },
            estimatedDates: {
                type: Object,
                default: null
            },
            showStatus: {
                type: Boolean,
                default: false
            },
            configDate: {
                type: Object,
                default: null
            },
            calculateDate: {
                type: Boolean,
                default: false
            },
            configDeliveryDate: {
                type: Object,
                default: null
            },
            orderDate: {
                type: String,
                default: ''
            },
            deliveryType: {
                type: String,
                default: ''
            }
        }, //['cart', 'promotions'],
        data: function () {
        	return {
        		url: this.$store.getters.url + '/'
        	}
        },
		computed: {
            lang: function () {
				return this.$store.getters.lang;
            },
            displayStatus: function () {
                return this.cart[0].items[0].content.data.hasOwnProperty('order_detail');
            }
		}
    }
</script>

