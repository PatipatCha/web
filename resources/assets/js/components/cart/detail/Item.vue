<template>
    <div class="cart-pd-box" v-if="item.content">
        <div class="col-sm-7 col-xs-12 no-padding">
            <div class="pd-img">
                <div class="cart-pd-detail-box">
                    <a v-bind:href="item.content.data.url" v-bind:title="title">
                        <img class="img-responsive" v-bind:src="productImage" v-bind:alt="title">
                    </a>
                </div>
            </div>
            <div class="col-xs-9 col-sm-8">
                <div class="cart-pd-detail-box2 cart-text-detail">
                    <a v-bind:href="item.content.data.url" v-bind:title="title"><b>{{ title }}</b><br></a>
                    <span>{{ lang.product_code }} {{ code }}</span><br />
                    <b><span class="item-price">{{ price | money }} ฿ </span></b>
                    <s v-if="price < normalPrice">{{ normalPrice | money }} ฿</s>
                </div>
                <div class="cart-qty visible-xs" :class="{'has-warning': warningAmountText !== false}" v-if="canEdit">
                    <decrement v-bind:item="item" v-on:loading="updating" v-on:loaded="doneUpdating"></decrement>
                    <span class="qty-input" v-show="!loading"><manual v-bind:item="item" v-on:loading="updating" v-on:loaded="doneUpdating"></manual></span>
                    <i v-show="loading" class="far fa-refresh fa-spin fa-fw"></i>
                    <increment v-bind:item="item" v-on:loading="updating" v-on:loaded="doneUpdating"></increment>
                    <div class="clearfix"></div>
                </div>

                <!-- สำหรับหน้าแสดง detail เฉยๆ (Mobile mode) -->
                <div class="qty visible-xs" v-if="!canEdit">
                    <span>{{ lang.quantity }}</span> {{ item.quantity | number_comma }}
                </div>

                <div class="no-padding col-xs-12 visible-xs">
                    <div class="cart-pd-detail-box">
                        <span>
                            <b>{{ sub_total | money }} ฿</b>
                        </span>
                    </div>
                </div>

                <div class="visible-xs">
                    <remove v-bind:item="item" v-bind:lang="lang" v-if="canEdit"></remove>
                </div>
            </div>
            <div class="clearfix"></div>

            <div class="visible-xs">
                <div style="margin-top: 5px;" v-if="warningAmountText" class="text-danger" v-html="warningAmountText"></div>
            </div>
        </div>

        <div class="col-sm-2 hidden-xs no-padding"  v-if="canEdit">
            <div class="cart-pd-detail-box">
                <div class="cart-qty" :class="{'has-warning': warningAmountText !== false}">
                    <decrement v-bind:item="item" v-on:loading="updating" v-on:loaded="doneUpdating"></decrement>
                    <span class="qty-input">
                        <manual v-show="!loading" v-bind:item="item" v-on:loading="updating" v-on:loaded="doneUpdating"></manual>
                        <i v-show="loading" class="far fa-refresh fa-spin fa-fw"></i>
                    </span>
                    <increment v-bind:item="item" v-on:loading="updating" v-on:loaded="doneUpdating"></increment>
                    <div class="clearfix"></div>
                </div>
            </div>

            <!-- Warning amount -->
            <div style="text-align: center; margin-top: 5px;" v-if="warningAmountText" class="text-danger" v-html="warningAmountText"></div>
            <!-- Warning amount -->
        </div>

        <!-- สำหรับหน้าแสดง detail เฉยๆ (Desktop mode) -->
        <div class="col-sm-2 hidden-xs no-padding"  v-if="!canEdit">
            <div class="cart-pd-detail-box text-center">
                {{ item.quantity | number_comma }}
            </div>
        </div>

        <div class="col-sm-3 no-padding text-right hidden-xs">
            <div class="cart-pd-detail-box">
                <span>
                    <b>{{ sub_total | money }} ฿</b>
                </span>
            </div>
        </div>

        <div class="col-xs-9 col-xs-offset-3 hidden-xs">
            <remove v-bind:item="item" v-bind:lang="lang" v-if="canEdit"></remove>
        </div>

        <div class="clearfix"></div>
    </div>
</template>

<script>
    import Increment from './Increase'
    import Decrement from './Decrease'
    import Remove from './Remove'
    import Manual from './Manual'
    import StringHelper from '../../../libraries/StringHelper'
    import accounting from 'accounting'

    export default{
        name: 'cart-item',
        components: {
            'increment': Increment,
            'decrement': Decrement,
            'remove': Remove,
            'manual': Manual
        },
        props:{
            item: {
                required: true,
                type: Object
            },
            lang: {
                type: Object
            },
            canEdit: {
                type: Boolean,
                default: true
            }
        },
        data: function () {
            return {
                url: this.$store.getters.url,
                loading: false,
                warningAmountText: false
            }
        },
        methods: {
            updating: function () {
                this.loading = true;
            },

            doneUpdating: function () {
                this.loading = false;
            },
            update: function(item){

                this.loading = true

                var payload = {
                    item: item,
                    amount: item.quantity,
                    callback: this.loadingDone
                }
                this.$store.dispatch('updateCartItem', payload)
            },
            handleWarningAmount(item) {
                let min = null
                if (typeof item.content == 'object'
                    && typeof item.content.data == 'object'
                    && typeof item.content.data.minimum_order_limit == 'number'
                )
                {
                    min = item.content.data.minimum_order_limit
                }

                let max = null
                if (typeof item.content == 'object'
                    && typeof item.content.data == 'object'
                    && typeof item.content.data.maximum_order_limit == 'number'
                )
                {
                    max = item.content.data.maximum_order_limit
                }

                let stock = _.get(item, 'content.data.stock')
                if (typeof stock == 'number'
                    && stock < max
                )
                {
                    max = stock
                }

                this.warningAmountText = false

                //Check min
                if (min != null) {
                    if (item.quantity < min) {
                        this.warningAmountText = this.langObj.warning_minimum_order_limit.replace(':amount', accounting.formatNumber(min, 0))
                    }
                }

                //Check max
                if (max != null) {
                    if (item.quantity > max) {
                        this.warningAmountText = this.langObj.warning_maximum_order_limit.replace(':amount', accounting.formatNumber(max, 0))
                    }
                }
            }
        },
        computed: {
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
            sub_total: function () {
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
            title: function () {
                return StringHelper.htmlspecialcharDecode(this.item.content.data.name)
            },
            langObj()
            {
                return this.$store.getters.lang
            },
            code() {
                return this.item.content.data.id
            }
        },
        watch: {
            item() {
                this.handleWarningAmount(this.item)
            }
        },
        mounted() {
            console.log(this.item.content.data.url)
            this.handleWarningAmount(this.item)
        }
    }
</script>