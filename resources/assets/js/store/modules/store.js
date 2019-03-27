import {
    SELECT_STORE,
    SET_STORES,
    CHANGE_CONFIRM_CHANGE_STORE,
    SET_STORE_TO_CHANGE,
    SET_PRODUCT_WISH_LIST_REMOVE,
    SET_ADD_TO_WISH_LIST_SUCCESS,
    SET_ADD_TO_WISH_LIST_DATA,
    SET_SHOW_PICKER_PRODUCT_NOT_IN_STORE,
    SET_AVAILABLE_STORES,
    SET_ADDRESSES,
    SET_SELECTED_ADDRESS,
    SET_OPEN_POPUP_ADDRESS,
    ADD_ADDRESS,
    UPDATE_ADDRESS,
    UPDATE_PREVENT_MOVE_ACTIVE_ADDRESS_TO_TOP,
    SET_ERROR_STORE_NOTAVILABLE_MESSAGE,
    SET_RETURN_TO_CART_ON_CHANGE_ADDRESS
} from '../mutation-types'
import MakroStore from '../../libraries/store/makro_store'
import StoreAddress from '../../libraries/store/store_address'
import axios from 'axios'
import MakroStoreLib from '../../libraries/makro_store'
import Cart from '../../libraries/cart'
import {getAddressData} from '../../helpers/member'

const makroStore = new MakroStore()
const storeAddress = new StoreAddress()

const state = {
    current_store_id: makroStore.get(),
    stores: [],
    show_confirm_change_store: false,
    store_to_change: null,
    product_wish_list_remove: null,
    add_to_wish_list_success: false,
    add_to_wish_list_data: null,
    show_picker_product_not_in_store: false,
    availableStores: null,
    addresses: storeAddress.get(),
    selected_address: storeAddress.getSelected(),
    open_popup_address: storeAddress.getOpenPopup(),
    prevent_move_active_address_to_top: false,
    error_store_not_avilable_message: '',
    return_to_cart: false
}

const mutations = {
    [SELECT_STORE](state, id) {
        id = parseInt(id);
        state.current_store_id = id
        makroStore.set(id);
    },

    [SET_STORES](state, stores) {
        state.stores = stores;
    },

    [CHANGE_CONFIRM_CHANGE_STORE](state, status) {
        state.show_confirm_change_store = status
    },

    [SET_STORE_TO_CHANGE](state, store) {
        state.store_to_change = store
    },

    [SET_PRODUCT_WISH_LIST_REMOVE](state, product) {
        state.product_wish_list_remove = product
    },

    [SET_ADD_TO_WISH_LIST_SUCCESS](state, data) {
        state.add_to_wish_list_success = data
    },

    [SET_ADD_TO_WISH_LIST_DATA](state, obj) {
        state.add_to_wish_list_data = obj
    },

    [SET_SHOW_PICKER_PRODUCT_NOT_IN_STORE](state, status) {
        state.show_picker_product_not_in_store = status;
    },
    [SET_AVAILABLE_STORES](state, availableStores) {
        state.availableStores = availableStores
    },
    [SET_ADDRESSES](state, payload) {
        state.addresses = payload
    },
    [SET_SELECTED_ADDRESS](state, address) {
        state.selected_address = address
    },
    [SET_OPEN_POPUP_ADDRESS](state, status) {
        state.open_popup_address = status
    },
    [ADD_ADDRESS](state, payload) {
        state.addresses.unshift(payload)
    },
    [UPDATE_ADDRESS](state, payload) {
        let addresses = _.clone(state.addresses)
        addresses[payload.index] = payload.data
        state.addresses = addresses;
    },
    [UPDATE_PREVENT_MOVE_ACTIVE_ADDRESS_TO_TOP](state, bool) {
        state.prevent_move_active_address_to_top = bool;
    },
    [SET_ERROR_STORE_NOTAVILABLE_MESSAGE] (state, text) {
        state.error_store_not_avilable_message = text
    },
    [SET_RETURN_TO_CART_ON_CHANGE_ADDRESS] (state, status) {
        state.return_to_cart = status
    }

}

const getters = {
    currentStore: state => state.current_store_id,
    currentStoreData: state => {
        var selectedStore = null;
        for (var i = 0; i < state.stores.length; ++i) {
            if (state.current_store_id == state.stores[i].id) {
                selectedStore = state.stores[i]
                break;
            }
        }

        return selectedStore
    },
    storeDeliverAddresses : state => {
        if (state.prevent_move_active_address_to_top === false) {
            const index  = state.addresses.findIndex((item) => {
                return item.id == _.get(state.selected_address, 'id')
            })

            if (index > 0) {
                let addresses = _.cloneDeep(state.addresses)
                const activeAddress = addresses[index]
                addresses.splice(index, 1)

                return [
                    activeAddress,
                    ...addresses
                ]
            }
        }

        return state.addresses
    }
}

const actions = {
    selectStore({state, commit, getters}, payload) {
        commit(SELECT_STORE, payload.store.id)
        makroStore.set(payload.store.id)

        //Save current store to API
        var makroStoreLib = new MakroStoreLib();
        makroStoreLib.setCurrentStoreToApi(getters, payload.store.id, function (response) {
            //Success
            var cart = new Cart();
            cart.receiveCartFromApi(getters, commit, function () {
                if (typeof payload.callback == 'object' || typeof payload.callback == 'function') {
                    payload.callback()
                }
            })

        }, function () {
            //Error
        });
    },

    getStoresFromApi({state, commit, rootState, getters}) {

        axios.get(getters.locale_url + '/stores')
            .then(function (response) {
                //Success
                commit(SET_STORES, response.data);
            })
            .catch(function () {
                //Error
            });

    },

    getActivePickupStoresFromApi({state, commit, rootState, getters}) {
        return new Promise((resolve, reject) => {
            axios.get(getters.locale_url + '/stores/pickup-store')
                .then(function (response) {
                    //Success
                    resolve(response)
                })
                .catch(function () {
                    //Error
                });
        });

    },

    getCurrentStoreFromApi({state, commit, rootState, getters}) {
        var makroStoreLib = new MakroStoreLib();
        makroStoreLib.getCurrentStoreFromApi(getters, function (response) {
            //Success
            if (response.data.status == 'ok') {
                commit(SELECT_STORE, response.data.data.store_id);
                makroStore.set(response.data.data.store_id);
            }
        }, function () {
            //Error
        })

    },

    setStoreAddress({state, commit, getters}, payload) {
        storeAddress.set(payload)
        commit(SET_ADDRESSES, payload)
    },

    getAddressFromApi({state, commit, getters}) {
        if (getters.logged_in) {
            axios.get(getters.locale_url + '/members/shipping-list').then(response => {
                let items = _.get(response.data, 'items', [])
                // if (items.length > 0) {
                    let newItems = items.map((item) => {
                        return getAddressData(item)
                    })

                let index = newItems.findIndex((item) => {
                    return item.id == _.get(state.selected_address, 'id')
                })

                if (index > 0) {
                    let item = newItems[index]
                    newItems.splice(index, 1)
                    newItems = [
                        item,
                        ...newItems
                    ]
                }

                    storeAddress.set(newItems)
                    commit(SET_ADDRESSES, newItems)
                // }
            }).catch(error => {
                console.log(error)
            })
        }
    },


    setSelectedAddress({state, commit, getters}, address) {
        storeAddress.setSelected(address)
        commit(SET_SELECTED_ADDRESS, address)

    },

    addAddress({state, commit}, payload) {
        storeAddress.addAddress(payload)
        commit(ADD_ADDRESS, getAddressData(payload))
    },

    updateAddress({state, commit}, payload) {
        let index = state.addresses.findIndex((item) => {
            return payload.id === item.id
        })

        if (index > -1) {
            payload = {
                index,
                data: getAddressData(payload)
            }

            commit(UPDATE_ADDRESS, payload)
            storeAddress.updateAddress(payload)
        }
    },

    getDeliveryStoreByPostCode({state, commit, getters}, postcode) {
        return new Promise((resolve, reject) => {
            axios.get(`${getters.locale_url}/address/delivery-by-postcode?postcode=${postcode}`)
                .then(response => {
                    resolve(response)
                })
                .catch(error => {
                    reject(error)
                })
        })
    },

    updateCurrentStore({state, commit, getters}, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`${getters.locale_url}/stores/set-current-store`, payload)
                .then(response => {
                    resolve(response)
                })
                .catch(error => {
                    reject(error)
                })
        })
    },
    updatePopupAddress({state, commit, getters}, status) {
        commit(SET_OPEN_POPUP_ADDRESS, status)
    }
}

export default {
    state,
    mutations,
    getters,
    actions
}