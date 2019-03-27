<template>
    <div>
        <modal :show="showPicker" v-on:cancel="cancel" v-bind:title="title" v-on:on-close-modal="onCloseModal" content-size="normal" v-bind:show-ok="true" v-bind:show-cancel="true" v-bind:show-loading="showLoading" v-on:on-ok="select">
            <div>
                <div class="select-store-text2">
                    {{ message }}
                </div>
                <div class="dropdown-select-store">
                    <select class="form-control store-picker" name="test" v-model="selectedStore">
                        <option value="">{{ lang.please_select_makro_store }}</option>
                        <option v-for="(store, index) in stores()" v-bind:value="store.id" v-bind:checked="index == 0">{{ store.name }}</option>
                    </select>
                </div>
            </div>
        </modal>
    </div>
</template>

<script>
    import Modal from '../bootstrap/Modal2'
    import { CHANGE_SHOW_PICKUP_STORE, SET_SHOW_PICKUP_STORE_CALLBACK } from '../../store/mutation-types'
    import Loader from '../loading/Loading'

    export default {
        name: 'store-picker',
        components: {
            'modal': Modal,
            'loader': Loader
        },
        data: function () {
            return {
                icon: this.$store.getters.url + '/assets/images/icon-Dropdown-B.png',
                showPicker: false,
                callback: null,
                store: null,
                selectedStore: '',
                showLoading: false
            }
        },
        mounted: function () {
            var me = this

            this.$store.subscribe((mutation, state) => {
                switch (mutation.type) {
                    case CHANGE_SHOW_PICKUP_STORE:
                        if (mutation.payload == true) {
                            me.showPicker = true;
                            me.callback = state.appModule.showStorePickupCallback
                        }

                        me.$store.commit(SET_SHOW_PICKUP_STORE_CALLBACK, null)
                        break;
                }
            })

            $('.store-picker').on('change', (e) => {
                const stores = this.stores()
                this.selectedStore = ''
                if (e.target.value && !isNaN(parseInt(e.target.value)) && parseInt(e.target.value) > 0) {
                    this.selectedStore = e.target.value
                }
            })
        },
        computed: {
            storeName: function () {
                if (!this.store) {
                    return 'Pickup Store'
                }

                return this.store.name
            },
            lang: function () {
                return this.$store.getters.lang
            },
            title: function () {
                if (this.$store.state.storeModule.availableStores && this.$store.state.storeModule.availableStores.length > 0) {
                    return this.$store.getters.lang.stores_with_goods
                }
                return this.$store.getters.lang.please_select_makro_store
            },
            message: function () {
                if (this.$store.state.storeModule.availableStores && this.$store.state.storeModule.availableStores.length > 0) {
                    return this.$store.getters.lang.product_not_available_in_your_current_makro_store
                }
                return this.$store.getters.lang.pickup_store
            }
        },
        methods: {
            select: function () {
                var me = this;

                if (this.selectedStore) {
                    let store = null
                    const stores = this.stores()
                    for (let i = 0; i < stores.length; ++i) {
                        if (stores[i].id == this.selectedStore) {
                            store = stores[i]
                            break
                        }
                    }


                    var me = this
                    var payload = {
                        store: store,
                        callback: function () {
                            me.showPicker = false;
                            me.showLoading = false;
                            me.callback();
                        }
                    }

                    this.showLoading = true;
                    this.$store.dispatch('selectStore', payload)
                }

            },
            cancel: function () {
                this.showPicker = false
            },
            onCloseModal: function () {
                this.showPicker = false
            },
            stores: function () {

                if (this.$store.state.storeModule.availableStores && this.$store.state.storeModule.availableStores.length > 0) {
                    return this.getAvailableStores(this.$store.state.storeModule.availableStores)
                }

                return this.$store.state.storeModule.stores
            },
            getAvailableStores: function (availableStores) {
                var stores = this.$store.state.storeModule.stores
                var filterStores = stores.filter(function(item) {
                    return availableStores.indexOf(item.id) != -1 ? true : false
                })

                return filterStores
            }
        }
    }
</script>