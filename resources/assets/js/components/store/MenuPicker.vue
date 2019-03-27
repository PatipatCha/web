<template>
    <div class="box-select-store" v-if="showStoreSelector == 1">
        <div class="btn-group">
            <button type="button" class="btn2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="text-overflow">{{ storeName }}</span>
                <img v-bind:src="icon" width="10"/>
            </button>
            <ul class="dropdown-menu">
                <li v-for="store in stores"><a href="javascript:;" v-on:click="select(store)">{{ store.name }}</a></li>
                <li><a href="http://www.makroclick.com">{{ lang.select_other_stores }}</a></li>
            </ul>
        </div>
    </div>

</template>

<script>
    import { CHANGE_CONFIRM_CHANGE_STORE, SET_STORE_TO_CHANGE } from '../../store/mutation-types'
    import popup from '../../libraries/popup'
    import { mapGetters } from 'vuex'

    export default {
        name: 'store-menu-picker',
        data: function (){
            return {
                icon: this.$store.getters.url + '/assets/images/icon-Dropdown-W.png',
                selectedStore: null
            }
        },
        computed: {
            ...mapGetters([
                'showStoreSelector'
            ]),
            storeName: function () {
                var storeData = this.$store.getters.currentStoreData
                if (!storeData) {
                    if (!this.$store.getters.currentStore || this.$store.getters.currentStore < 1) {
                        return 'Makro Store'
                    } else {
                        return ''
                    }
                }

                return storeData.name
            },
            stores: function () {
                var stores = this.$store.state.storeModule.stores
                var newStores = stores.slice();

                if (!this.$store.getters.currentStore || this.$store.getters.currentStore < 1) {
                    newStores.unshift({
                        id: null,
                        name: 'Makro Store'
                    })
                }


                return newStores
            },
            count: function() {
                return this.$store.getters.cartCount;
            },
            lang: function () {
                return this.$store.getters.lang
            }

        },
        methods: {
            select: function (store) {
                var me = this
                this.selectedStore = store
                if (store.id != this.$store.getters.currentStore) {
                    if (this.count > 0) {
                        var message = this.lang.your_product_is_in_current_store_if_you_want_to_change_the_price_will_be_change
                        message = message.replace(/:store_name/gi, store.name)

                        popup.open({
                            title: this.lang.branches_to_receive_the_goods,
                            message: message,
                            type: 'confirm',
                            size: 'wide',
                            center: false,
                            confirm: function (done) {
                                var payload = {
                                    'callback': function () {
                                        done()
                                        popup.close()
                                        me.onChangeStoreCompleted()
                                    },
                                    'store': store
                                };

                                me.$store.dispatch('selectStore', payload);
                            }
                        })
                    } else {
                       this.changeStore();
                    }

                }
            },

            changeStore()
            {
                window.location = this.$store.getters.locale_url + '/stores/set-current-store-redirect?store_id=' + this.selectedStore.id
            },

            onChangeStoreCompleted: function () {
                if (!window.location.href.match(/\/carts/gi)) {
                    window.location.reload()
                }
            }
        },
    }
</script>