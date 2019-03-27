<template>
    <div class="panel panel-default shipping-type">
        <a class="collapsed" role="button" data-toggle="collapse" href="#product-non-assortment-with-installment-tab" aria-expanded="false" aria-controls="product-non-assortment-with-installment-tab">
            <div class="panel-heading" role="tab" id="heading-product-non-assortment-with-installment-tab">
                <h4 class="panel-title">
                    <product-type-icon type="non_assortment_with_installment"></product-type-icon> {{ lang.non_assortment_with_installment }}
                    <span class="remark">({{ groupData.items.length | number_comma}} item)</span>
                    <product-type-icon-information type="non_assortment_with_installment"></product-type-icon-information>
                </h4>
            </div>
        </a>
        <div id="product-non-assortment-with-installment-tab" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading-product-non-assortment-with-installment-tab">
            <div class="panel-body">
                <div class="info-box">
                    <label class="error" for="product_non_assortment_with_installment[delivery_method]"></label>
                    <div class="radio">
                        <label for="customer-address">
                            <input type="radio" id="customer-address" name="product_non_assortment_with_installment[delivery_method]" v-model="delivery_method" value="same_customer_address" required v-on:click="onSelectDeriveryMethod"> {{ lang.delivery_same_as_buyer_information }}
                        </label>
                    </div>
                    <div class="message-box" v-if="delivery_method == 'same_customer_address'">
                        <p>{{ memberAddress.shop_name }}</p>
                        <p>{{ memberAddress.first_name }} {{ memberAddress.last_name }}</p>
                        <p><full-address v-bind:address="memberAddress"></full-address></p>
                        <p><b>{{ lang.tel }}</b> {{ memberAddress.phone }} <b>{{ lang.e_mail }}</b> {{ memberAddress.email }}</p>
                    </div>
                </div>
                <div class="info-box other-shipping">
                    <div class="radio">
                        <label for="other-address">
                            <input type="radio" id="other-address" name="product_non_assortment_with_installment[delivery_method]" v-model="delivery_method" value="other" required v-on:click="onSelectDeriveryMethod"> {{ lang.delivery_other_address }}
                        </label>
                    </div>

                    <div v-if="delivery_method == 'other'">
                        <shipping-address v-bind:addresses="shippingAddresses" type="cart" v-bind:selectable="true" v-bind:lang="lang"></shipping-address>
                    </div>

                </div>

                <cart-checkout-product-list v-bind:items="groupData.items" product-type="non_assortment_with_installment"></cart-checkout-product-list>

            </div>
        </div>
    </div>
</template>

<script>
    import DatePicker from '../../../input_fields/DatePicker'
    import PickerTime from '../fields/PickerTime'
    import ProductList from '../product_type/list/List'
    import ProductTypeIcon from '../ProductTypeIcon'
    import ShippingAddress from '../shipping_address/Container'
    import FullAddress from '../address/FullAddress'
    import ProductTypeIconInformation from './../ProductTypeIconInformation'


    export default {
        name: 'cart-checkout-product-non-assortment-installment',
        props: [
            'groupData',
            'memberAddress',
            'shippingAddresses'
        ],
        components: {
            'date-picker-field': DatePicker,
            'picker-time': PickerTime,
            'cart-checkout-product-list': ProductList,
            'product-type-icon': ProductTypeIcon,
            'shipping-address': ShippingAddress,
            'full-address': FullAddress,
            'product-type-icon-information': ProductTypeIconInformation
        },
        data: function () {
            return {
                url: this.$store.getters.url + '/',
                delivery_method: '',

            }
        },
        methods: {
            onSelectDeriveryMethod: function () {
                $('.input_shipping_address_ids').each(function () {
                    $(this).prop('checked', false);
                });
            }  
        },
        computed: {
            lang: function () {
                return this.$store.getters.lang;
            }
        }
    }
</script>