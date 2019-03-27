<template>
    <div class="summary-box-table">
        <div class="cart-pd-table">
            <div class="topic-table4">
                <div class="head text-bold">
                    <div class="title">
                        <product-type-icon v-bind:type="groupAlias"></product-type-icon>
                        <b>{{ groupTitle }}</b>
                    </div>
                    <div class="item-qty">
                        <span class="item-count">{{ item.items.length | number_comma }}</span> {{ itemText }}
                        <product-type-icon-information
                                v-bind:type="this.item.alias"
                                :direct-content="sidebarDescription"
                                :btn-id="item.sidebar_id_btn"
                        >
                        </product-type-icon-information>
                    </div>
                </div>
                <div v-if="calculateDate"><p v-html="deliveryDateText"></p></div>
            </div>

            <group-item
                    v-bind:display-status="showStatus"
                    v-bind:item="product"
                    :alias="item.alias"
                    :key="product.id"
                    v-for="product in item.items"
                    :estimated-dates="estimatedDates"
                    :delivery-type="deliveryType"
            ></group-item>
        </div>
    </div>
</template>

<script>
    import ProductTypeIcon from '../ProductTypeIcon'
    import GroupItem from './GroupItem'
    import ProductTypeIconInformation from './../ProductTypeIconInformation'
    import pluralize from 'pluralize'
    import {trans} from "../../../../libraries/trans"
    import moment from 'moment'

    export default {
        name: 'cart-checkout-summary-group',
        props: {
            item: {
                type: Object,
                required: true
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
            orderDate: {
                type: String,
                default: ''
            },
            configDeliveryDate: {
                type: Object,
                default: null
            },
            deliveryType: {
                type: String,
                default: ''
            }
        },
        components: {
            ProductTypeIcon,
            GroupItem,
            'product-type-icon-information': ProductTypeIconInformation
        },
        data: function () {
            return {
                url: this.$store.getters.url + '/'
            }
        },
        computed: {
            lang: function () {
                return this.$store.getters.lang;
            },
            itemText() {
                if (GLOBAL_SETTING.current_locale == 'th') {
                    return this.$store.getters.lang.item
                }
                return pluralize(this.$store.getters.lang.item_singular, this.item.items.length)
            },
            groupTitle() {
                // if(_.get(this.selectedAddress, 'type') == 'store') {
                //     return 'pickup-stocked'
                // }
                if(this.deliveryType == 'pickup' && this.item.alias == 'stocked') {
                    return trans('pickup_stocked')
                }
                if(this.deliveryType == 'pickup' && this.item.alias == 'preorder') {
                    return trans('pickup_preorder')
                }
                if(this.deliveryType == 'shipping' && this.item.alias == 'stocked') {
                    return trans('stoked_product_title')
                }
                if(this.deliveryType == 'shipping' && this.item.alias == 'preorder') {
                    return trans('preorder_product_title')
                }
                if(_.get(this.selectedAddress, 'type') == 'store' && this.item.alias == 'stocked') {
                    return trans('pickup_stocked')
                }
                if(_.get(this.selectedAddress, 'type') == 'store' && this.item.alias == 'preorder') {
                    return trans('pickup_preorder')
                }
                if(_.get(this.selectedAddress, 'type') == 'address' && this.item.alias == 'stocked') {
                    return trans('stoked_product_title')
                }
                if(_.get(this.selectedAddress, 'type') == 'address' && this.item.alias == 'preorder') {
                    return trans('preorder_product_title')
                }
                return this.item.title
            },
            groupAlias() {
                if(this.deliveryType == 'pickup' && this.item.alias == 'stocked') {
                    return 'pickup-stocked'
                }
                if(this.deliveryType == 'pickup' && this.item.alias == 'preorder') {
                    return 'pickup-preorder'
                }
                if(this.deliveryType == 'shipping' && this.item.alias == 'stocked') {
                    return 'stocked'
                }
                if(this.deliveryType == 'shipping' && this.item.alias == 'preorder') {
                    return 'preorder'
                }
                if(_.get(this.selectedAddress, 'type') == 'store' && this.item.alias == 'stocked') {
                    return 'pickup-stocked'
                }
                if(_.get(this.selectedAddress, 'type') == 'store' && this.item.alias == 'preorder') {
                    return 'pickup-preorder'
                }
                return this.item.alias
            },
            selectedAddress() {
                return this.$store.state.storeModule.selected_address
            },
            sidebarDescription() {
                if(this.configDate) {
                    if (_.get(this.selectedAddress, 'type') == 'address' && this.item.alias == 'stocked') {
                        let times = _.get(this.configDate, 'deliveryDate')
                        let group = _.first(_.filter(times, (item) => {
                            return item.group == 'product_instock'
                        }))

                        let min_max = _.get(group, 'min') + '-' + _.get(group, 'max')
                        return '<div id="lbl_information_ready_to_pickup_product">' +
                            trans('stocked_product_delivery_description', {time: min_max}) +
                            '</div>'
                    }
                    if (_.get(this.selectedAddress, 'type') == 'address' && this.item.alias == 'preorder') {
                        let times = _.get(this.configDate, 'deliveryDate')
                        let group = _.first(_.filter(times, (item) => {
                            return item.group == 'product_preorder'
                        }))

                        let min_max = _.get(group, 'min') + '-' + _.get(group, 'max')
                        return '<div id="lbl_information_ready_to_pickup_product">' +
                            trans('preorder_product_delivery_description', {time: min_max}) +
                            '</div>'
                    }
                    if (_.get(this.selectedAddress, 'type') == 'store' && this.item.alias == 'stocked') {
                        let times = _.get(this.configDate, 'pickupDate')
                        let group = _.first(_.filter(times, (item) => {
                            return item.group == 'product_instock'
                        }))

                        let min_max = _.get(group, 'min') + '-' + _.get(group, 'max')
                        return '<div id="lbl_information_ready_to_pickup_product">' +
                            trans('stocked_product_pickup_description', {time: min_max}) +
                            '</div>'
                    }
                    if (_.get(this.selectedAddress, 'type') == 'store' && this.item.alias == 'preorder') {
                        let times = _.get(this.configDate, 'pickupDate')
                        let group = _.first(_.filter(times, (item) => {
                            return item.group == 'product_preorder'
                        }))

                        let min_max = _.get(group, 'min') + '-' + _.get(group, 'max')
                        return '<div id="lbl_information_ready_to_pickup_product">' +
                            trans('preorder_product_pickup_description', {time: min_max}) +
                            '</div>'
                    }
                }
                return this.item.sidebar_description
            },
            deliveryDateText() {
                if (this.calculateDate) {
                    let group = this.getMinMax
                    moment.locale(this.locale);
                    let add_year = 0
                    if (this.locale == 'th') {
                        add_year = 543
                    }
                    let from_dm = moment(this.orderDate).add(_.get(group, 'min', 2), 'days').format('DD MMMM');
                    let from_y = parseInt(moment(this.orderDate).add(_.get(group, 'min', 2), 'days').format('Y')) + add_year;
                    let from = from_dm + ' ' + from_y
                    let to_dm = moment(this.orderDate).add(_.get(group, 'max', 4), 'days').format('DD MMMM');
                    let to_y = parseInt(moment(this.orderDate).add(_.get(group, 'max', 4), 'days').format('Y')) + add_year;
                    let to = to_dm + ' ' + to_y
                    if(this.deliveryType == 'pickup') {
                        return trans('text_pickup_from_to', {from: from, to: to})
                    }
                    if (this.deliveryType == 'shipping') {
                        return trans('text_delivery_from_to', {from: from, to: to})
                    }
                    if(_.get(this.selectedAddress, 'type') == 'store') {
                        return trans('text_pickup_from_to', {from: from, to: to})
                    }
                    if (_.get(this.selectedAddress, 'type') == 'address') {
                        return trans('text_delivery_from_to', {from: from, to: to})
                    }
                }
                return ''
            },
            getMinMax() {
                if(this.item.alias == 'stocked') {
                    return _.first(_.filter(this.configDeliveryDate, (item) => {
                        return item.group == 'product_instock'
                    }))
                }
                if(this.item.alias == 'preorder') {
                    return _.first(_.filter(this.configDeliveryDate, (item) => {
                        return item.group == 'product_preorder'
                    }))
                }
            },
            locale: function () {
                return this.$store.getters.locale
            }
        }
    }
</script>