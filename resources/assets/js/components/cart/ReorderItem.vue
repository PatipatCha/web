<template>
    <div>

        <div>
            <div class="clearfix">
                <p v-html="lang.items_can_not_be_ordered_or_not_enough_for_the_number_you_want_to_order"></p>
            </div>

            <div class="scroller">
                <div class="thead clearfix">
                    <div class="col-lg-7 col-md-8 col-sm-10 col-sm-8 col-xs-8 no-padding ">
                        <div class="topic-table1">
                            <b>{{ lang.product_details }}</b>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-2 no-padding hidden-sm hidden-xs">
                        <div class="topic-table2">
                            <b>{{ lang.ordered_quantity }}</b>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 no-padding">
                        <div class="topic-table3">
                            <b>{{ lang.subtotal }}</b>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="cart-pd-box" v-for="product in items">
                    <div class="col-lg-7 col-md-8 col-sm-10 col-sm-8 col-xs-8 no-padding ">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 no-padding hidden-xs">
                            <div class="cart-pd-detail-box cart-pd-img ">
                                <img class="cart-pd-img" v-bind:src="productImage(product.item)">
                            </div>
                        </div>
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 no-padding">
                            <div class="cart-pd-detail-box2 cart-text-detail text-left">
                                <b>{{ title(product.item) }}</b><br>
                                {{ lang.product_code }} {{ code(product.item) }}
                                <br />
                                <b><span class="remark">{{ price(product.item) | money }} ฿ </span></b>
                                <!--{{ lang.per }} {{ item.content.data.price_per_key }}<br>-->
                                <br />
                                <s v-if="price(product.item) < normalPrice(product.item)">{{ normalPrice(product.item) | money }} ฿</s>
                                <!--<div v-if="productType == 'non_assortment_with_installment' && getEstimatedDeliveryDate(item) != ''" class="remark">-->
                                <!--{{ getEstimatedDeliveryDate(item) }}-->
                                <!--</div>-->

                                <div v-html="quantityDescription(product, true)" class="hidden-lg hidden-md"></div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="col-lg-3 col-md-2 no-padding hidden-sm hidden-xs">
                        <div class="cart-pd-detail-box text-center">
                            <!--<div class="cart-qty">-->
                            <!--&lt;!&ndash;{{ item.quantity | number_comma }}&ndash;&gt;-->
                            <!--</div>-->
                            <div v-html="quantityDescription(product)"></div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 no-padding">
                        <div class="cart-pd-detail-box">
                            <div class="clearfix"></div>
                            <div class="cart-subtotal">
                                <b>{{ sub_total(product) }}</b>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import StringHelper from '../../libraries/StringHelper'
    import DateTime from '../../libraries/DateTime'
    import accounting from 'accounting'

    export default {
        name: "reorder-item",
        props: {
            items: {
                type: Array,
                required: true
            }
        },
        computed: {
            lang: function () {
                return this.$store.getters.lang;
            }
        },
        methods: {
            price: function (item) {
                return parseFloat(_.get(item, 'price', 0));
            },
            normalPrice: function (item) {
                return parseFloat(_.get(item, 'normal_price', 0));
            },
            sub_total: function (product) {
                switch (product.status) {

                    case 'not_enough_inventory':
                        return accounting.format(parseInt(product.available_quantity) * parseFloat(product.item.price), 2) + ' ฿'
                        break;
                }

                return '-'
            },
            productImage: function (item) {
                if (typeof item.thumbnail == 'string') {
                    return item.thumbnail;
                }
                return '';
            },
            title: function (item) {
                return StringHelper.htmlspecialcharDecode(item.name)
            },
            quantityDescription(product, isMobile = false) {
                switch (product.status) {
                    case 'unpublished':
                        return this.lang.unpublished_product_item
                        break;
                    case 'out_of_stock':
                        return this.lang.product_out_of_stock_error
                        break;
                    case 'not_enough_inventory':
                        if (isMobile) {
                            return _.replace(this.lang.only_quantities_are_available, '{items}', parseInt(product.available_quantity))
                        } else {
                            let text = '(' + _.replace(this.lang.only_quantities_are_available, '{items}', parseInt(product.available_quantity))+ ')'

                            return parseInt(product.request_quantity) + '<br />' + text
                        }
                        break;
                }

                return ''
            },
            code(item)  {
                return item.id
            }
        },
    }
</script>

<style scoped>

</style>