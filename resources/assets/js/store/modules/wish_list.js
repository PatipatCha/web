import * as types from '../mutation-types'
import 'jquery-toast-plugin/dist/jquery.toast.min.js'
import 'jquery-toast-plugin/dist/jquery.toast.min.css'
import WishListStore from '../../libraries/store/wish_list_store'
import WishList from '../../libraries/wish_list'

const wishListStore = new WishListStore()
const wishList = new WishList()
//State
var items = wishListStore.get()
const state = {
    items: items
}

//Mutations
const mutations = {
    [types.ADD_TO_WISH_LIST] (state, payload) {
        state.items.push(payload)
    },
    [types.REMOVE_FROM_WISH_LIST] (state, index) {

        state.items.splice(index, 1)
    },
    [types.UPDATE_WISH_LIST_ITEMS] (state, items) {
        state.items.length = 0;
        state.items = items;

        wishListStore.update(items)
    },
}


//Getters
const getters = {
    countWishList: state => state.items.length
}

//Actions
const actions = {
    addWishList({state, commit, getters, rootState}, payload) {
        commit(types.ADD_TO_WISH_LIST, payload)
    },

    addToWishList({state, commit, getters}, payload) {
        var product = payload.product
        var data = {
            'store_id': getters.currentStore,
            'content_id': product.id,
        };

        wishList.add(getters, data, function(response) {
            //Success

            if (typeof response.data.status == 'string' && response.data.status == 'ok') {
                payload.callback();
                wishList.onAddSuccess(response, commit);
            } else {
                //Update wish list items if exists
                if (typeof response.data.data == 'object' || typeof response.data.data == 'array') {
                    commit(types.UPDATE_WISH_LIST_ITEMS, response.data.data);
                }

                //Check duplicate
                var duplicate = false;
                if (typeof response.data.error_data == 'object') {
                    if (response.data.error_data.duplicate == true) {
                        duplicate = true;
                    }
                }

                if (duplicate) {
                    payload.callback(true);
                } else {
                    payload.callback(false, true)
                    wishList.onAddError(response, commit, getters);
                }


            }
        }, function (response) {
            //Error
            payload.callback(false, true);
            wishList.onAddError(response, commit, getters);
        });
    },

    removeFormWishList({state, commit, getters, rootState}, payload) {

        var product = payload.product
        var data = {
            'store_id': getters.currentStore,
            'content_id': product.id,
        };

        wishList.remove(getters, data, function(response) {
            payload.callback(true);

            //Success
            if (typeof response.data.status == 'string' && response.data.status == 'ok') {
                wishList.onRemoveSuccess(response, commit);
            } else {
                //Error
                wishList.onRemoveError(response, commit, getters);
            }
        }, function (response) {
            //Error
            payload.callback();
            wishList.onRemoveError(response, commit, getters);
        });
    },

    getWishLists({state, commit, getters, rootState}) {
        var param = {
            'store_id': getters.currentStore
        };

        axios.get(getters.locale_url + '/favorites', param)
            .then(function (response) {
                if (typeof response.data.status == 'string' && response.data.status == 'ok') {
                    commit(types.UPDATE_WISH_LIST_ITEMS, response.data.data);
                }
            })
            .catch(function () {

            });
    }
}

export default {
    state,
    mutations,
    getters,
    actions
}