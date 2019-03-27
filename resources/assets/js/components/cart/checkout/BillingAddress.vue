<template>
    <div>
        <div class="checkbox">
            <label>
                <input type="checkbox" v-model="useSameAddress" name="billing_address_same" value="1"> {{ lang.using_billing_address_same_as_tax_invoice_address }}
            </label>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding pull-right" v-if="!useSameAddress">
            <div class="form-group pay-pickup" >
                <residence-address
                        input-name="billing_address"
                        v-bind:init-data="address"
                >
                </residence-address>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</template>

<script>
    import cartCheckoutAddress from './address/Address'

    export default {
        name: 'cart-checkout-billing-address',
        components: {
            'cart-checkout-address': cartCheckoutAddress
        },
        props: {
            address: {
                type: Object,
                default: null
            }
        },
        data: function () {
            return {
                useSameAddress: true
            }
        },
        computed: {
            lang: function () {
                return this.$store.getters.lang;
            }
        }
    }
</script>