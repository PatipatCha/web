<template>
    <div class="cart-pd-box clearfix">
        <div class="no-padding pd-img-col col-sm-6 col-xs-12" v-bind:class="col1Width">
            <div class="pd-img">
                <img v-bind:src="productImage" class="img-responsive">
            </div>
            <div class="col-xs-8 col-sm-8">
                <div class="item-detail">
                    <div><b>{{ title }}</b></div>
                    <div>{{ lang.product_code }} {{ item.content.data.id }}</div>
                    <div class="price"><b>{{ price | money }} ฿</b> {{ lang.per }} {{ item.content.data.price_per_key }}
                    </div>
                    <div class="normal-price" v-if="price < normalPrice"><s>{{ normalPrice | money }} ฿</s></div>

                    <div v-if="refund_items.length > 0" class="alert-box">
                        <ul style="padding-left: 20px; margin: 0;">
                            <!--<li style="color: #ff3300;" v-for="refund in refund_items">{{lang.refund_items}} {{lang.amount}} {{refund.quantity}}  {{lang.item}} {{lang.total_price}} {{ refund.amount }}฿ {{lang.on_date}} {{ getDate(refund.date)}} </li>-->
                            <li style="color: #ff3300;" v-for="cancel in refund_items">
                                {{lang.refund_items}}
                                {{lang.amount}} {{cancel.quantity}} {{cancel.quantity > 1 ? lang.items:lang.item}}
                                {{lang.total_price}} {{ cancel.amount
                                }}฿ {{lang.on_date}} {{ getDate(cancel.date)}}
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="visible-xs print-hidden">
                    <div class="no-padding" v-bind:class="col2Width">
                        <span class="qty"><b>{{ lang.qty }} {{ item.quantity | number_comma }}</b></span>
                    </div>
                    <div class="no-padding" v-bind:class="col3Width">
                        <span class="cart-subtotal"><b>{{ lang.subtotal }} {{ subTotal | money }} ฿</b></span>
                    </div>
                    <div class="no-padding col-xs-12 pending" v-if="hasItemStatus">
                        <div class="icon">
                            <img :src="itemStatusIcon" />
                        </div>
                        <div class="body">
                            <b v-html="itemStatus">
                                <!-- {{ itemStatus }}
                                <span v-if="showEstimateDeliveryDate"><br />{{ lang.estimate_delivery_date_order_history }}<br></span>
                                <span v-if="showEstimateDeliveryDate" class="item-price">{{ getEstimatedDeliveryDate }}</span> -->
                            </b>
                        </div>
                    </div>
                </div>

                <!-- <div v-if="hasItemStatus" class="pd-status-col hidden-xs hidden-sm print-hidden">
                    <div class="pending">
                        <div class="icon">
                            <img :src="itemStatusIcon" />
                        </div>
                        <div class="body">
                            <b v-html="itemStatus">
                                <span v-if="showEstimateDeliveryDate">
                                    {{ lang.estimate_delivery_date_order_history }}
                                </span>
                                <span v-if="showEstimateDeliveryDate" class="item-price">{{ getEstimatedDeliveryDate }}</span>
                            </b>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>

        <div class="no-padding text-center hidden-xs pd-qty-col" v-bind:class="col2Width">
            <span class="qty"><b>{{ item.quantity | number_comma }}</b></span>
        </div>
        <div class="no-padding text-right hidden-xs pd-price-col" v-bind:class="col3Width">
            <span class="cart-subtotal"><b>{{ subTotal | money }} ฿</b></span>
        </div>

        <div v-if="hasItemStatus" class="col-xs-4 col-sm-4 col-md-3 pd-status-col hidden-xs">
            <div class="pending hidden-xs">
                <div class="icon">
                    <img :src="itemStatusIcon"/>
                </div>
                <div class="body">
                    <b v-html="itemStatus">
                        <!--<span v-if="showEstimateDeliveryDate">
                            {{ lang.estimate_delivery_date_order_history }}
                        </span>
                        <span v-if="showEstimateDeliveryDate" class="item-price">{{ getEstimatedDeliveryDate }}</span>-->
                    </b>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import DateTime from '../../../../libraries/DateTime'
    import moment from 'moment'
    import StringHelper from '../../../../libraries/StringHelper'

    const dateTime = new DateTime()

    export default {
        name: 'cart-checkout-summary-group-item',
        props: {
            item: {
                type: Object,
                required: true
            },
            displayStatus: {
                default: false
            },
            alias: {
                type: String,
                required: true,
                default: ""
            },
            estimatedDates: {
                type: Object,
                default: null
            }
        },
        computed: {
            title: function () {
                let originalName = _.get(this.item, 'content.data.original_name.' + GLOBAL_SETTING.current_locale)

                if (originalName && originalName.length) {
                    return StringHelper.htmlspecialcharDecode(originalName)
                }

                originalName = _.get(this.item, 'content.original_name.' + GLOBAL_SETTING.current_locale)
                if (originalName && originalName.length) {
                    return StringHelper.htmlspecialcharDecode(originalName)
                }

                if (typeof this.item.content.name == 'string') {
                    return StringHelper.htmlspecialcharDecode(this.item.content.name)
                } else if (typeof this.item.content.data.name == 'string') {
                    return StringHelper.htmlspecialcharDecode(this.item.content.data.name)
                } else if (typeof this.item.content.data.title == 'string') {
                    return StringHelper.htmlspecialcharDecode(this.item.content.data.title)
                }

                return '';
            },
            price: function () {
                var price = 0;
                if (this.item.price.data) {
                    price = this.item.price.data.price
                }

                return parseFloat(price);
            },
            normalPrice: function () {
                var price = 0;
                if (this.item.price.data) {
                    price = this.item.price.data.normal_price
                }

                return parseFloat(price);
            },
            subTotal: function () {
                var price = 0;
                if (this.item.price.data) {
                    price = this.item.price.data.price
                }
                return price * this.item.quantity
            },
            productImage: function () {
                if (typeof this.item.content.data.thumbnail == 'string') {
                    return this.item.content.data.thumbnail;
                }
                return '';
            },
            lang: function () {
                return this.$store.getters.lang;
            },
            deliveryDate: function () {
                if (!_.isEmpty(this.estimatedDates)) {
                    return _.get(this.estimatedDates, this.alias, '')
                }

                return ''
            },
            pickupTimeStart: function () {
                if (this.item.content.data.hasOwnProperty('pickup_time_start')) {
                    var pickupTimeStart = this.item.content.data.pickup_time_start;
                    return pickupTimeStart;
                }
                return '';
            },
            pickupTimeEnd: function () {
                if (this.item.content.data.hasOwnProperty('pickup_time_end')) {
                    var pickupTimeEnd = this.item.content.data.pickup_time_end;
                    return pickupTimeEnd;
                }
                return '';
            },
            col1Width: function () {
                return {
                    'col-xs-12 col-sm-7': this.hasItemStatus || this.displayStatus,
                    'col-xs-12 col-sm-7': !this.displayStatus
                } // มีโอกาสที่ item_status ส่งส่งค่า null หรือ '' มา
            },
            col2Width: function () {
                return {
                    'col-xs-12 col-sm-1': this.hasItemStatus || this.displayStatus,
                    'col-xs-12 col-sm-2': !this.displayStatus
                } // มีโอกาสที่ item_status ส่งส่งค่า null หรือ '' มา
            },
            col3Width: function () {
                return {
                    'col-xs-12 col-sm-3': this.hasItemStatus || this.displayStatus,
                    'col-xs-12 col-sm-3': !this.displayStatus
                } // มีโอกาสที่ item_status ส่งส่งค่า null หรือ '' มา
            },
            productType: function () {
                if (typeof this.alias == 'string') {
                    return this.alias;
                }
                return '';
            },
            pickupDate: function () {
                // if (typeof this.item.content.data.pickup_date == 'string' && dateTime.isValidDate(this.item.content.data.pickup_date)) {
                //     var date = dateTime.displayDate(this.item.content.data.pickup_date, this.$store.state.appModule.locale);
                //     return this.lang.estimated_delivery_date.replace(':date', date);
                // }
                // return '';
            },

            cancel_items: function () {
                if (this.item.content.data.hasOwnProperty('cancel')) {
                    return this.item.content.data.cancel.history;
                }
                return [];
            },
            refund_items: function () {
                if (this.item.content.data.hasOwnProperty('refund')) {
                    return this.item.content.data.refund.history;
                }
                return [];
            },
            return_items: function () {
                if (this.item.content.data.hasOwnProperty('return')) {
                    return this.item.content.data.return.history;
                }
                return [];
            },
            getEstimatedDeliveryDate() {
                return _.get(this.item, 'content.data.est_delivery_date.delivery_date', '')
            },
            hasItemStatus() {
                if (!this.displayStatus) {
                    return false
                }
                // if (this.refund_items.length > 0) {
                //     let item = _.first(this.refund_items)
                //     if (item.quantity !== parseInt(this.item.quantity)) {
                //         return false
                //     }
                // }
                // if (this.return_items.length > 0) {
                //     return false
                // }

                if (_.get(this.item, 'content.data.item_status')) {
                    return true
                }
                return false
            },
            itemStatus() {
                if (this.refund_items.length > 0) {
                    let item = _.first(this.refund_items)
                    if (item.quantity === parseInt(this.item.quantity)) {
                        return this.lang.order_item_status_shipping_cancel_order
                    }
                }
                return _.get(this.item, 'content.data.item_status_text', '')
            },
            isItemCancelled() {
                return _.get(this.item, 'content.data.is_cancelled', false)
            },
            itemStatusIcon() {
                if (this.refund_items.length > 0) {
                    let item = _.first(this.refund_items)
                    if (item.quantity === parseInt(this.item.quantity)) {
                        return this.$store.getters.url + '/images/item_status_canceled.png'
                    }
                }
                return _.get(this.item, 'content.data.item_status_icon', '')
            },
            showEstimateDeliveryDate() {
                if (_.get(this.item, 'content.data.is_cancelled', false) === true
                    || (_.get(this.item, 'content.data.delivery_type') === 'pickup' && _.get(this.item, 'content.data.item_status') === 'shipped')
                    || (_.get(this.item, 'content.data.delivery_type') === 'shipping' && _.get(this.item, 'content.data.item_status') === 'delivered')
                    || _.get(this.item, 'content.data.item_status') === 'pending'
                ) {
                    return false
                }

                if (this.refund_items.length > 0) {
                    let item = _.first(this.refund_items)
                    if (item.quantity === parseInt(this.item.quantity)) {
                        return false
                    }
                }

                return true
            }
        },
        methods: {
            getDate: function (date) {
                var formatDate = dateTime.displayDate(date, this.$store.state.appModule.locale)
                if (formatDate !== false) {
                    return formatDate
                }

                return '';
            }
        },
        mounted: function () {
            moment.locale(this.$store.state.appModule.locale);
        }
    }
</script>