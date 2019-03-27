<template>
    <div role="tabpanel" class="tab-pane" :class="active? 'active': ''" id="pickup">
        <div class="loading" v-if="showLoadingAddress">
            <i class="fas fa-sync fa-spin fa-2x"></i>
        </div>
        <div v-if="!showLoadingAddress" class="pickup">
            <div><p>{{ pickup_title_message }}</p></div>
            <div class="row">
                <div class="col-sm-5 form-group">
                    <v-select @click="console.log(this)" ref="select_province" :loading="showLoadingAddress" v-model="selected_province" :options="provinces"
                              :clearable="false" :searchable="false"></v-select>
                </div>
                <div class="col-sm-7 form-group store-select">
                    <v-select :loading="showLoadingAddress" v-model="selected_store" :options="stores"
                              :clearable="false" :searchable="false"></v-select>
                </div>
            </div>
            <div class="clearfix"></div>
            <google-map :store-selected="selected_store"
                        :on-click-store-from-map="confirmSelectStore"
                        :lat-lng="latLng" ref="GoogleMap"
                        :stores="stores" v-if="!showLoadingAddress"
                        :all-store="allStore"
            ></google-map>
        </div>
        <div v-if="!showLoadingAddress">
            <button class="btn btn-default pull-left" v-if="selected_address" @click="cancel"><i
                    class="far fa-times"></i> {{ trans('cancel') }}
            </button>
            <button @click="submit" class="btn btn-primary btn-block pull-right">
                <i v-if="!submiting" class="far fa-check"></i>
                <Loading :show="submiting"></Loading>
                {{ trans('ok') }}
            </button>
        </div>
    </div>
</template>

<script>
    import GoogleMap from './GoogleMap'
    import {mapActions} from 'vuex'
    import vSelect from 'vue-select'
    import Loading from '../../../components/loading/Loading'
    import {SET_OPEN_POPUP_ADDRESS, SET_ERROR_STORE_NOTAVILABLE_MESSAGE} from '../../../store/mutation-types'
    import { trans } from '../../../libraries/trans';

    export default {
        name: "Pickup",
        components: {
            GoogleMap,
            vSelect,
            Loading
        },
        data() {
            return {
                provinces: [],
                stores: [],
                province_name: 'กรุงเทพฯ',
                selected_store: null,
                showLoadingAddress: false,
                allStore: null,
                latLng: null,
                selected_province: null,
                is_loading: false,
                default_store: null,
                addressId: ''
            }
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
            onSubmitConfirmChangeAddress: {
                type: Function,
                default: () => {
                }
            },
            submiting: {
                type: Boolean,
                default: false
            },
            type: {
                type: String,
                default: ''
            }
        },
        computed: {
            latitude: function () {
                return _.get(this.selected_address, 'location.lat', 13.7563309)
            },
            longtitude: function () {
                return _.get(this.selected_address, 'location.lng', 100.5017651)
            },
            selected_address: function () {
                return this.$store.state.storeModule.selected_address
            },
            pickup_title_message: function() {
                return _.get(GLOBAL_SETTING, 'pickup_title_message')
            }
        },
        methods: {
            ...mapActions(['getActivePickupStoresFromApi']),
            selectProvince() {
                let locale = this.$store.getters.locale
                let stores = _.get(this.selected_province, 'items')
                this.stores = _.map(stores, (store) => {
                    let branch_name = _.get(store,'name')
                    if(locale === 'th') {
                        branch_name = _.split(branch_name, 'สาขา', 2)
                        branch_name = 'สาขา'+branch_name[1]
                    }else {
                        branch_name = _.split(branch_name, 'Branch', 2)
                        branch_name = 'Branch '+branch_name[1]
                    }
                    return {label: branch_name, data: store}
                })
                // this.allStore = this.stores
                if (this.default_store) {
                    this.selected_store = _.cloneDeep(this.default_store)
                    this.default_store = null
                } else {
                    if(this.addressId.length === 0) {
                        this.selected_store = _.first(this.stores)
                    }else{
                        let select_store = _.filter(this.stores, (item) => {
                            return item.data.address.id === this.addressId
                        })
                        
                        this.selected_store = _.first(select_store)
                        if(!this.selected_store) {
                            this.selected_store = _.first(this.stores)
                        }
                        this.addressId = ''
                    }
                    
                }
                if(!this.selected_address && !this.selected_store) {
                    this.getLocationByProvince()
                }
            },
            selectStore() {
                if(this.selected_store) {
                    this.latLng = _.get(this.selected_store, 'data.address.location')
                    let id = _.get(this.selected_store, 'data.address.id')
                    this.onClickStoreFromMap(id)
                }
            },
            confirmSelectStore(id) {
                this.addressId = id
                this.onClickStoreFromMap(id)
            },
            onClickStoreFromMap(id) {
                // this.$store.commit(SET_ERROR_STORE_NOTAVILABLE_MESSAGE, '')
                if (id) {
                    let store = _.filter(this.allStore, (item) => {
                        return item.data.address.id == id
                    })
                    this.selected_store = _.head(store)
                    let locale = this.$store.getters.locale
                    let branch_name = _.get(this.selected_store,'data.name')
                    if(locale === 'th') {
                        branch_name = _.split(branch_name, 'สาขา', 2)
                        branch_name = 'สาขา'+branch_name[1]
                    }else {
                        branch_name = _.split(branch_name, 'Branch', 2)
                        branch_name = 'Branch '+branch_name[1]
                    }
                    this.selected_store.label = branch_name
                    store = _.get(_.head(store), 'data')
                    store.address.store_price = _.get(store, 'store_price')
                    store.address.main_inventory_store = _.get(store, 'main_inventory_store')
                    store.address.name = _.get(store, 'original_name')
                    store.store_id = _.get(store, 'id')
                    this.selected_province = _.first(_.filter(this.provinces, (item) => {
                        return item.label == _.get(this.selected_store, 'data.address.province')
                    }))
                    this.onSelectAddress(store)
                }
            },
            submit() {
                this.onSubmitConfirmChangeAddress()
            },
            getLocationByProvince() {

                let geocoder = new google.maps.Geocoder();
                geocoder.geocode({'address': _.get(this.selected_province, 'label', 'กรุงเทพ')}, (results, status) => {
                    if (status == 'OK') {
                        this.latLng = {
                            lat: results[0].geometry.location.lat(),
                            lng: results[0].geometry.location.lng()
                        }
                    } else {
                        alert('Geocode was not successful for the following reason: ' + status);
                    }
                });
                // let geoUrl = 'https://maps.googleapis.com/maps/api/geocode/json'
                // let params = `?address=${province}&components=country:TH`
                //
                // return axios(geoUrl+params)

            },
            cancel() {
                this.$store.commit(SET_OPEN_POPUP_ADDRESS, false)
            },
            initialData() {
                if(this.type == 'store') {
                    this.showLoadingAddress = true
                    this.getActivePickupStoresFromApi().then(response => {
                        let id = ''
                        if (this.selected_address) {
                            if (this.selected_address.type == 'store') {
                                this.province_name = _.get(this.selected_address, 'province')
                                id = _.get(this.selected_address, 'id')
                            }
                        }

                        let res = _.filter(response.data, (item) => {
                            return typeof item.address.province === 'string'
                        })
                        this.allStore = _.map(res, (item) => {
                            return {label: item.address.province, data: item}
                        })

                        let province = _.groupBy(res, 'address.province')
                        this.provinces = _.map(province, (items, index) => {
                            return {label: index, items: items}
                        })
                        this.selected_province = _.first(_.filter(this.provinces, (item) => {
                            return item.label == this.province_name
                        }))
                        let bkk = _.first(_.filter(this.provinces, (item) => {
                            return item.label == 'กรุงเทพฯ'
                        }))
                        
                        this.provinces = _.sortBy(this.provinces, 'label')
                        if(bkk) {
                            this.provinces = _.concat(bkk, this.provinces)
                        }
                        this.provinces = _.uniqBy(this.provinces, 'label')
                        // this.selected_province = null
                        if(!this.selected_province) {
                            this.selected_province = _.first(this.provinces)
                            if(this.selected_address && this.selected_address.type === 'store') {
                                this.$store.commit(SET_ERROR_STORE_NOTAVILABLE_MESSAGE, trans('pickup_store_address_not_avilable'))
                            }
                        }
                        let defaultStore = _.filter(this.selected_province.items, (item) => {
                            return item.address.id == id
                        })
                        if (defaultStore.length > 0) {
                            this.default_store = {
                                data: _.head(defaultStore),
                                label: _.get(_.head(defaultStore), 'name')
                            }
                        }
                        this.$refs.GoogleMap.initMap()

                        this.showLoadingAddress = false
                    }).catch(e => {
                        this.showLoadingAddress = false
                    })
                    if (this.selected_address) {
                        this.latLng = _.get(this.selected_address, 'location')
                    } else {
                        this.latLng = {
                            lat: this.latitude,
                            lng: this.longtitude
                        }
                    }
                }
            }
        },
        mounted() {
            this.initialData()
        },
        watch: {
            selected_province: function () {
                this.selectProvince()
            },
            selected_store: function () {
                this.selectStore()
            },
            type: function () {
                if(this.type == 'store') {
                    this.initialData()
                }
            }
        }

    }
</script>

<style scoped>

</style>