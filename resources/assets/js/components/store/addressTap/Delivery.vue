<template>
    <div role="tabpanel" class="tab-pane" :class="active? 'active': ''" id="delivery">
        <div class="loading" v-if="showLoadingAddress">
            <i class="fas fa-sync fa-spin fa-2x"></i>
        </div>
        <div v-if="errorAddressType" class="remark">{{ error_type_message }}</div>
    
        <div v-show="!showLoadingAddress" class="choose-address" :class="addresses.length > 3? 'more-address': ''">
            <div><p>{{ shipping_title_message }}</p></div>
            <div class="header clearfix" v-if="show_select_address_box">
                <div class="title">{{ trans('select_address') }}</div>
                <a href="#" id="btn_popup_add_address" v-if="logged_in" class="new-address"
                   @click="() => { onAddAddress() }">
                    <i class="far fa-plus"></i> {{ trans('add_address') }}
                </a>
            </div>
            <div class="content-scroll" ref="list">
                <div class="panel panel-default" :class="address.id === addressId ? 'active' : ''"
                     v-for="(address, index) in addresses"
                     @click="selectAddress(address, $event)" :key="index">
                    <div class="panel-body" :class="address.delivery ? '': 'disabled'">
                        <div class="media">
                            <div class="media-left hidden-xs">
                                <i class="far fa-store-alt"></i>
                            </div>
                            <div class="media-body media-top">
                                <h4 class="media-heading">{{ getFullName(address) }}</h4>
                                <p>
                                    <full-address join=" " :address="address"></full-address>
                                </p>
                            </div>
                            <div v-if="logged_in" class="media-right">
                                <a v-if="address.delivery" :id="'btn_popup_edit_address-'+index" href="javascript:;" class="edit">{{
                                    trans('edit') }}</a>
                            </div>
                            <div v-if="!address.delivery" class="media-right">
                                <a href="javascript:;">{{
                                    trans('out_of_delivery_service') }}</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div v-show="!showLoadingAddress" class="post-code">
            <div class="header">
                <div class="title">{{ trans('enter_postcode') }}</div>
            </div>

            <input-postcode v-if="showModal" :style="'display: '+display_input" :address-data="address"
                            ref="InputPostcode"
                            :on-select-address="selectAddressFromInput"
                            :on-update-postcode="updatePostcode"
                            :address-type="address_type"></input-postcode>

            <div v-if="address_type == 'postcode'" class="panel panel-default active">
                <div class="panel-body">
                    <div class="media">
                        <div class="media-left hidden-xs">
                            <i class="far fa-store-alt"></i>
                        </div>
                        <div class="media-body media-top">
                            <h4 class="media-heading">{{ trans('postcode') }}</h4>
                            <p>{{ getDistrictName(address) }} {{ getProvinceName(address) }} {{ this.postcode }}</p>
                        </div>
                        <div class="media-right">
                            <a id="btn_popup_edit_postcode" @click="editPostcode" href="#">{{ trans('edit') }}</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="error" v-if="submitError && submitError.length > 0">
                {{ submitError }}
            </div>

            <div class="row padding-top15">
                <div class="col-xs-6">
                    <button
                            v-if="selected_address"
                            type="button"
                            class="btn btn-default"
                            @click="cancel"
                            id="btn_popup_cancel_change_exiting_shipping_address"
                    >
                        <i class="far fa-times"></i>
                        {{ trans('cancel')}}
                    </button>
                </div>

                <div class="col-xs-6 pull-right">
                    <button
                            id="btn_popup_save_shipping_address"
                            type="button"
                            class="btn btn-primary pull-right"
                            @click="onConfirm"
                            :disabled="submitting"
                    >
                        <i class="far fa-check" v-if="!submitting"></i>
                        <loading :show="submitting"></loading>
                        {{ trans('ok') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import FullAddress from '../../cart/checkout/address/FullAddress'
    import Loading from '../../loading/Loading'
    import Scroll from '../../../libraries/Scroll'
    import {trans} from "../../../libraries/trans"
    import Autocomplete from 'v-autocomplete'
    import InputPostcode from './InputPostcode'
    import {mapState, mapActions} from 'vuex'
    import { SET_OPEN_POPUP_ADDRESS, SET_ERROR_STORE_NOTAVILABLE_MESSAGE } from '../../../store/mutation-types'

    Vue.component('v-autocomplete', Autocomplete)

    const scroll = new Scroll

    export default {
        name: "Delivery",
        components: {
            FullAddress,
            Loading,
            InputPostcode
        },
        props: {
            active: {
                type: Boolean,
                default: false
            },
            onSelectAddress: {
                type: Function,
                default: () => {
                }
            },
            addressId: {
                type: String,
                default: ''
            },
            onConfirm: {
                type: Function,
                default: () => {
                }
            },
            onAddAddress: {
                type: Function,
                default: () => {
                }
            },
            onEdit: {
                type: Function,
                default: () => {
                }
            },
            errorAddressType: {
                type: Boolean,
                default: false
            },
            submitting: {
                type: Boolean,
                default: false
            },
            submitError: {
                type: String,
                default: ''
            },
            addressData: {
                type: Object,
                default: null
            },
            type: {
                type: String,
                default: ''
            }
        },
        data() {
            return {
                postcode: '',
                address_type: '',
                loading: false,
                showLoadingAddress: false
            }
        },
        computed: {
            ...mapState({
                selected_address: state => state.storeModule.selected_address
            }),
            showModal: function () {
                // return false
                if (!this.$store.state.storeModule.selected_address) {
                    return true
                }
                return this.$store.state.storeModule.open_popup_address
            },
            addresses: function () {
                let data = this.$store.state.storeModule.addresses
                if(data) {
                    data = _.orderBy(data, ['delivery'], 'desc')
                }
                return data
            },
            locale: function () {
                return this.$store.getters.locale
            },
            logged_in: function () {
                return this.$store.getters.logged_in
            },
            show_select_address_box() {
                let show = true
                if (_.isEmpty(this.addresses)) {
                    show = false
                }

                return show
            },
            address: function () {
                return this.addressData
            },
            show_input: function () {
                return this.address_type != 'postcode' || !this.address_type
            },
            display_input: function () {
                if (this.show_input) {
                    return 'block'
                }

                return 'none'
            },
            error_type_message: function () {
                if(!this.address && this.postcode && !this.addresses) {
                    return trans('please_check_zip_code')
                }
                return trans('please_select_address_or_enter_postal_code')
            },
            select_type: function () {
                return this.type
            },
            shipping_title_message: function() {
                return _.get(GLOBAL_SETTING, 'shipping_title_message')
            }
        },
        methods: {
            ...mapActions(['setStoreAddress']),
            getFullName(address) {
                return _.get(address, 'first_name', '') + ' ' + _.get(address, 'last_name', '')
            },
            selectAddress(address, e) {
                this.$store.commit(SET_ERROR_STORE_NOTAVILABLE_MESSAGE, '')
                if(address.delivery) {
                    this.address_type = 'address'
                    if (e.target.className.match(/edit/)) {
                        //Edit address
                        this.onEdit(address)
                    } else {
                        let payload = {
                            address: address,
                            type: this.address_type,
                            store: null
                        };
                        this.onSelectAddress(payload)
                    }
                    this.$refs.InputPostcode.updatePostcode(this.address_type, '')
                }
            },
            editPostcode() {
                this.address_type = ''

                let data = {
                    address: null,
                    type: '',
                    store: null
                };
                this.onSelectAddress(data)
                this.$refs.InputPostcode.updatePostcode(this.address_type, this.address)
            },
            scrollToBottom() {
                scroll.top(this.$refs.list)
            },
            selectAddressFromInput(payload) {
                this.address_type = payload.type
                this.postcode = payload.address.postcode
                let data = {
                    address: payload.address,
                    type: this.address_type,
                    store: null
                };
                // this.address = payload.address
                this.onSelectAddress(data)
            },
            getDistrictName(address) {
                return _.get(address, 'district.original_name.'+ this.locale)
            },
            getProvinceName(address) {
                return _.get(address, 'province.original_name.' + this.locale)
            },
            updateType(type) {
                this.address_type = type
            },
            cancel() {
                this.$store.commit(SET_OPEN_POPUP_ADDRESS, false)
            },
            updatePostcode(text) {
                let postcode = text
                if(typeof text == 'object') {
                    postcode = _.get(text, 'postcode')
                }
                this.postcode = postcode
            },
            initialData() {
                if(this.logged_in) {
                    this.showLoadingAddress = true
                    this.$store.subscribe((mutation, state) => {
                        if (mutation.type == 'SET_ADDRESSES') {
                            this.showLoadingAddress = false

                            if (this.selected_address) {
                                if (this.select_type == 'address' && this.selected_address.type == 'address') {
                                    let addressData = _.filter(this.addresses, item => {
                                        return item.id == this.selected_address.id
                                    })

                                    if (_.isEmpty(addressData)) {
                                        this.address_type = 'postcode'
                                        this.postcode = this.selected_address.postcode
                                        let address = this.selected_address
                                        address.id = null
                                        let data = {
                                            address: address,
                                            type: this.address_type,
                                            store: null
                                        };
                                        this.showLoadingAddress = true
                                        axios.get(`${this.$store.getters.locale_url}` + '/address/delivery-by-postcode?postcode=' + `${this.postcode}`).then(response => {
                                            let address = response.data
                                             this.showLoadingAddress = false
                                            if (address.length <= 0) {
                                                this.address_type = ''
                                                data.address = null
                                                this.$store.commit(SET_ERROR_STORE_NOTAVILABLE_MESSAGE, trans('delivery_store_address_not_avilable'))
                                            } else {
                                                this.$refs.InputPostcode.updatePostcode('postcode', this.selected_address)
                                                
                                            }
                                            this.onSelectAddress(data)
                                           
                                        }).catch(error => {
                                            this.showLoadingAddress = false
                                        })
                                        // this.address = payload.address
                                       
                                    } else {
                                        let address_data = _.head(addressData)
                                        this.address_type = !this.selected_address.id ? 'postcode' : 'address'
                                        if(address_data.delivery == false) {
                                            address_data = null
                                            this.address_type = 'address'
                                            this.$store.commit(SET_ERROR_STORE_NOTAVILABLE_MESSAGE, trans('delivery_store_address_not_avilable'))
                                        }
                                    
                                        this.postcode = this.selected_address.postcode
                                        let data = {
                                            address: address_data,
                                            type: this.address_type,
                                            store: null
                                        };
                                        // this.address = payload.address
                                        this.onSelectAddress(data)
                                        setTimeout(() => {
                                            console.log(document.querySelector('.panel-default.active'))
                                            //scroll.to(document.querySelector('.panel-default.active'), true)
                                        }, 300)
                                    }
                                }
                            }
                        }
                    })
                }else {
                    if (this.selected_address) {
                        if (this.select_type == 'address' && _.get(this.selected_address, 'type') == 'address') {
                            console.log('init')
                            let addressData = _.filter(this.addresses, item => {
                                return item.id == this.selected_address.id
                            })

                            if (_.isEmpty(addressData)) {

                                this.address_type = 'postcode'
                                this.postcode = this.selected_address.postcode
                                let address = this.selected_address
                                address.id = null
                                let data = {
                                    address: address,
                                    type: this.address_type,
                                    store: null
                                };
                                this.showLoadingAddress = true
                                axios.get(`${this.$store.getters.locale_url}` + '/address/delivery-by-postcode?postcode=' + `${this.postcode}`).then(response => {
                                    let address = response.data
                                        this.showLoadingAddress = false
                                    if (address.length <= 0) {
                                        console.log(address)
                                        this.address_type = ''
                                        data.address = null
                                        this.$store.commit(SET_ERROR_STORE_NOTAVILABLE_MESSAGE, trans('delivery_store_address_not_avilable'))
                                    } else {
                                        this.$refs.InputPostcode.updatePostcode('postcode', this.selected_address)
                                    
                                    }
                                    this.onSelectAddress(data)
                                    
                                }).catch(error => {
                                    this.showLoadingAddress = false
                                })
                            } else {
                                let address_data = _.head(addressData)
                                this.address_type = !this.selected_address.id ? 'postcode' : 'address'
                                if(address_data.delivery == false) {
                                    address_data = null
                                    this.address_type = 'address'
                                    this.$store.commit(SET_ERROR_STORE_NOTAVILABLE_MESSAGE, trans('delivery_store_address_not_avilable'))
                                }
                            
                                this.postcode = this.selected_address.postcode
                                let data = {
                                    address: address_data,
                                    type: this.address_type,
                                    store: null
                                };
                                // this.address = payload.address
                                this.onSelectAddress(data)
                                setTimeout(() => {
                                    console.log(document.querySelector('.panel-default.active'))
                                    //scroll.to(document.querySelector('.panel-default.active'), true)
                                }, 300)
                            }
                        }
                    }
                }
            },
            checkInitialData() {
                this.showLoadingAddress = false

                if(this.logged_in) {
                    this.showLoadingAddress = true
                }
            }
        },
        mounted() {

        },
        watch: {
            select_type: function () {
                if(this.select_type == 'address') {
                    this.initialData()
                }
            }
        }
    }
</script>

<style scoped>

</style>