<template>
    <div>
        <modal :show="showPicker" v-on:cancel="cancel" v-bind:title="lang.stores_with_goods" v-on:on-close-modal="onCloseModal">
            <div>
                <div class="select-store-text2">
                    {{ lang.selected_store_has_no_this_product_please_select_new_store }}
                </div>
                <div class="dropdown-select-store">
                    <select class="form-control" name="test" v-model="selectedStore">
                        <option value="">{{ lang.please_select_makro_store }}</option>
                        <option v-for="(store, index) in stores" v-bind:value="store" v-bind:checked="index == 0">{{ store.name }}</option>
                    </select>
                </div>
            </div>
            <div slot="footer">
                <button type="button" class="btn btn-primary" v-on:click="select">
                    {{ lang.btn_confirm }} <loader v-bind:show="showLoading"></loader>
                </button>
            </div>
        </modal>
    </div>
</template>

<script>
    import Modal from '../bootstrap/Modal'
    import Loader from '../loading/Loading'
    import { SET_SHOW_PICKER_PRODUCT_NOT_IN_STORE } from '../../store/mutation-types'

    export default {
        name: 'store-picker-product-not-in-store',
        components: {
            'modal': Modal,
            'loader': Loader
        },
        data: function () {
            return {
                selectedStore: null,
                showLoading: false
            }
        },
        methods: {
            onCloseModal: function () {
                this.$store.commit(SET_SHOW_PICKER_PRODUCT_NOT_IN_STORE, false)
            },
            cancel: function () {

            },
            select: function () {
                var me = this

                me.showLoading = true;

                if (this.selectedStore) {
                    var payload = {
                        store: this.selectedStore,
                        callback: function () {
                            //me.$store.commit(SET_SHOW_PICKER_PRODUCT_NOT_IN_STORE, false)
                            //me.showLoading = false;
                            window.location.reload();
                        }
                    }

                    this.showLoading = true;
                    this.$store.dispatch('selectStore', payload);
                }
            }
        },
        computed: {
            stores: function () {
                return this.$store.state.storeModule.stores
            },
            storeName: function () {
                if (!this.store) {
                    return 'Pickup Store'
                }

                return this.store.name
            },
            showPicker: function () {
                return this.$store.state.storeModule.show_picker_product_not_in_store;
            },
            lang: function () {
                return this.$store.getters.lang;
            }
        },
    }
</script>