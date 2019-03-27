<template>
    <div>
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
        <div class="cart-pd-box" v-for="item in items">
            <div class="col-lg-7 col-md-8 col-sm-10 col-sm-8 col-xs-8 no-padding ">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 no-padding hidden-xs">
                    <div class="cart-pd-detail-box cart-pd-img ">
                        <img class="cart-pd-img" v-bind:src="productImage(item)">
                    </div>
                </div>
                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 no-padding">
                    <div class="cart-pd-detail-box2 cart-text-detail">
                        <b>{{ title(item) }}</b><br>
                        {{ lang.product_code }} {{ code(item) }}
                        <br />
                        <b><span class="remark">{{ price(item) | money }} ฿ </span></b>
                        <!--{{ lang.per }} {{ item.content.data.price_per_key }}<br>-->
                        <br />
                        <s v-if="price(item) < normalPrice(item)">{{ normalPrice(item) | money }} ฿</s>
                        <!--<div v-if="productType == 'non_assortment_with_installment' && getEstimatedDeliveryDate(item) != ''" class="remark">-->
                            <!--{{ getEstimatedDeliveryDate(item) }}-->
                        <!--</div>-->
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="col-lg-3 col-md-2 no-padding hidden-sm hidden-xs">
                <div class="cart-pd-detail-box text-center">
                    <!--<div class="cart-qty">-->
                        <!--&lt;!&ndash;{{ item.quantity | number_comma }}&ndash;&gt;-->
                    <!--</div>-->
                    <div v-html="quantityDescription(item)"></div>
                </div>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 no-padding">
                <div class="cart-pd-detail-box">
                    <div class="cart-qty2 hidden-lg hidden-md">
                        <div v-html="quantityDescription(item)"></div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="cart-subtotal">
                        <b>{{ sub_total(item) }}</b>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</template>

<script>
    import moment from 'moment'
    import StringHelper from '../../../../../libraries/StringHelper'
    import DateTime from '../../../../../libraries/DateTime'
    import accounting from 'accounting'

    export default {
        name: 'cart-checkout-product-list',
        props: {
            items: {
                type: Array,
                required: true
            },
            groupData: {
                type: Array
            },
            productType: {
                type: String
            }
        },
        data: function () {
            return {
                url: this.$store.getters.url + '/'
            }
        },
        methods: {
            price: function (item) {
                return parseFloat(_.get(item, 'price', 0));
            },
            normalPrice: function (item) {
                return parseFloat(_.get(item, 'normal_price', 0));
            },
            sub_total: function (item) {
                switch (item.info.error) {
                    case 'not_enough_inventory':
                        return accounting.format(parseInt(item.info.available) * parseFloat(item.price), 2) + ' ฿'
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
            getEstimatedDeliveryDate: function (item) {
                const dateTime = new DateTime()

                if (typeof item.content.data.pickup_date == 'string'
                    && dateTime.isValidDate(item.content.data.pickup_date)
                )
                {
                    const date = dateTime.displayDate(item.content.data.pickup_date, this.$store.state.appModule.locale)
                    return this.lang.estimated_delivery_date.replace(/:date/gi, date)
                }

                return ''

            },
            quantityDescription(item) {
              console.log(item)
                switch (item.info.error) {
                    case 'unpublished':
                        return this.lang.unpublished_product_item
                        break;
                    case 'not_enough_inventory':
                        let text = '(' + this.lang.not_enough_inventory_product_item.replace(/:available/, parseInt((item.info.request_quantity - item.info.available)))
                            .replace(/:request_quantity/, parseInt(item.info.request_quantity)) + ')'

                        return parseInt(item.info.available) + '<br />' + text
                        break;
                }

                return ''
            },
            code(item)  {
                return item.id
            }
        },
        computed: {
            lang: function () {
                return this.$store.getters.lang;
            }
        }
    }
</script>