<template>
    <div class="group-items">
        <div class="pd-category">
            <div class="topic-table4">
                <div class="row">
                    <div class="col-xs-7 col-sm-8">
                        <product-type-icon v-bind:type="groupAlias"></product-type-icon>
                        <b class="text-bold">{{ groupTitle }}</b>
                    </div>

                    <div class="col-xs-5 col-sm-4 text-right">
                        <span class="item-qty">
                            <span class="item-count">{{ items.length | number_comma}}</span>
                            {{ itemText }}
                        </span>
                        <product-type-icon-information v-bind:type="alias" placement="right auto" v-bind:lang="lang"
                                                       :direct-content="descriptionText"
                                                       :btn-id="btnId">
                        </product-type-icon-information>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-12">
                        <div :id="getDeliveryDateLabel"><b v-html="deliveryDate"></b></div>
                    </div>
                </div>
            </div>
            <!-- <div class="col-xs-9 col-sm-7 no-padding">
                <div class="topic-table1">
                    <b>{{ lang.product_details }}</b>
                </div>
            </div>
            <div class="col-xs-3 hidden-xs no-padding">
                <div class="topic-table2">
                    <b>{{ lang.qty }}</b>
                </div>
            </div>
            <div class="col-xs-3 col-sm-2 no-padding">
                <div class="topic-table3">
                    <b>{{ lang.subtotal }}</b>
                </div>
            </div> -->
            <div class="clearfix"></div>
        </div>
        <item
                v-bind:item="item"
                v-for="item in items"
                v-bind:lang="lang" :key="item.id"
                :can-edit="canEdit"
        >
        </item>
    </div>
</template>

<script>
    import Item from './Item'
    import ProductTypeIcon from '../checkout/ProductTypeIcon'
    import ProductTypeIconInformation from '../checkout/ProductTypeIconInformation'
    import pluralize from 'pluralize'
    import {trans} from "../../../libraries/trans"
    import moment from 'moment'

    export default {
        name: 'cart-group-items',
        data: function () {
            return {
                url: this.$store.getters.url + '/'
            }
        },
        props: {
            title: {
                required: true,
                type: String
            },
            items: {
                required: true,
                type: Array
            },
            alias: {
                type: String,
                required: true
            },
            canEdit: {
                type: Boolean,
                default: true
            },
            deliveryDates: {
                type: Object
            },
            description: {
                type: String,
                default: ''
            },
            btnId: {
                type: String,
                default: ''
            },
            deliveryDateLabel: {
                type: String,
                default: ''
            },
            configDate: {
                type: Object,
                default: null
            },
            calculateDate: {
                type: Boolean,
                default: false
            }
        },
        components: {
            'item': Item,
            'product-type-icon': ProductTypeIcon,
            'product-type-icon-information': ProductTypeIconInformation
        },
        computed: {
            lang: function () {
                return this.$store.getters.lang;
            },
            locale: function () {
                return this.$store.getters.locale
            },
            deliveryDate() {
                if (this.calculateDate) {
                    let group = this.getMinMax()
                    moment.locale(this.locale);
                    let add_year = 0
                    if (this.locale == 'th') {
                        add_year = 543
                    }
                    let from_dm = moment().add(_.get(group, 'min', 2), 'days').format('DD MMMM');
                    let from_y = parseInt(moment().add(_.get(group, 'min', 2), 'days').format('Y')) + add_year;
                    let from = from_dm + ' ' + from_y
                    let to_dm = moment().add(_.get(group, 'max', 4), 'days').format('DD MMMM');
                    let to_y = parseInt(moment().add(_.get(group, 'max', 4), 'days').format('Y')) + add_year;
                    let to = to_dm + ' ' + to_y
                    if(_.get(this.selectedAddress, 'type') == 'store') {
                        return trans('text_pickup_from_to', {from: from, to: to})
                    }else {
                        return trans('text_delivery_from_to', {from: from, to: to})
                    }
                }
                return _.get(this.deliveryDates, this.alias, '')
            },
            getDeliveryDateLabel() {
                if (_.isEmpty(this.deliveryDateLabel)) {
                    return false
                }

                return this.deliveryDateLabel
            },
            itemText() {
                if (GLOBAL_SETTING.current_locale == 'th') {
                    return this.$store.getters.lang.item
                }
                return pluralize(this.$store.getters.lang.item_singular, this.items.length)
            },
            groupTitle() {
                // if(_.get(this.selectedAddress, 'type') == 'store') {
                //     return 'pickup-stocked'
                // }
                if (_.get(this.selectedAddress, 'type') == 'store' && this.alias == 'stocked') {
                    return trans('pickup_stocked')
                }
                if (_.get(this.selectedAddress, 'type') == 'store' && this.alias == 'preorder') {
                    return trans('pickup_preorder')
                }
                return this.title
            },
            groupAlias() {
                if (_.get(this.selectedAddress, 'type') == 'store' && this.alias == 'stocked') {
                    return 'pickup-stocked'
                }
                if (_.get(this.selectedAddress, 'type') == 'store' && this.alias == 'preorder') {
                    return 'pickup-preorder'
                }
                return this.alias
            },
            selectedAddress() {
                return this.$store.state.storeModule.selected_address
            },
            descriptionText() {
                if (this.configDate) {
                    let group = this.getMinMax()
                    if (_.get(this.selectedAddress, 'type') == 'address' && this.alias == 'stocked') {
                        let min_max = _.get(group, 'min') + '-' + _.get(group, 'max')
                        return '<div id="lbl_information_ready_to_pickup_product">' +
                            trans('stocked_product_delivery_description', {time: min_max}) +
                            '</div>'
                    }
                    if (_.get(this.selectedAddress, 'type') == 'address' && this.alias == 'preorder') {

                        let min_max = _.get(group, 'min') + '-' + _.get(group, 'max')
                        return '<div id="lbl_information_ready_to_pickup_product">' +
                            trans('preorder_product_delivery_description', {time: min_max}) +
                            '</div>'
                    }
                    if (_.get(this.selectedAddress, 'type') == 'store' && this.alias == 'stocked') {

                        let min_max = _.get(group, 'min') + '-' + _.get(group, 'max')
                        console.log(min_max)
                        return '<div id="lbl_information_ready_to_pickup_product">' +
                            trans('stocked_product_pickup_description', {time: min_max}) +
                            '</div>'
                    }
                    if (_.get(this.selectedAddress, 'type') == 'store' && this.alias == 'preorder') {

                        let min_max = _.get(group, 'min') + '-' + _.get(group, 'max')
                        console.log(min_max)
                        return '<div id="lbl_information_ready_to_pickup_product">' +
                            trans('preorder_product_pickup_description', {time: min_max}) +
                            '</div>'
                    }
                }

                return this.description
            }
        },
        methods: {
            getMinMax() {
                if (this.configDate) {
                    if (_.get(this.selectedAddress, 'type') == 'address' && this.alias == 'stocked') {
                        let times = _.get(this.configDate, 'deliveryDate')
                        let group = _.first(_.filter(times, (item) => {
                            return item.group == 'product_instock'
                        }))

                        return group
                    }
                    if (_.get(this.selectedAddress, 'type') == 'address' && this.alias == 'preorder') {
                        let times = _.get(this.configDate, 'deliveryDate')
                        let group = _.first(_.filter(times, (item) => {
                            return item.group == 'product_preorder'
                        }))

                        return group
                    }
                    if (_.get(this.selectedAddress, 'type') == 'store' && this.alias == 'stocked') {
                        let times = _.get(this.configDate, 'pickupDate')
                        let group = _.first(_.filter(times, (item) => {
                            return item.group == 'product_instock'
                        }))

                        return group
                    }
                    if (_.get(this.selectedAddress, 'type') == 'store' && this.alias == 'preorder') {
                        let times = _.get(this.configDate, 'pickupDate')
                        let group = _.first(_.filter(times, (item) => {
                            return item.group == 'product_preorder'
                        }))

                        return group
                    }
                }

                return {min: 2, max: 3}
            }
        }
    }
</script>