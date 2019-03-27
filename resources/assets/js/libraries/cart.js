import Vue from 'vue'
import {
    SET_CART_ITEMS,
    SET_CART_SUMMARY,
    SET_ADD_TO_CART_SUCCESS,
    SET_ADD_TO_CART_SUCCESS_FROM_STORE,
    SET_ADD_TO_CART_DATA,
    SET_ADD_TO_CART_ERROR_LOW_STOCK,
    SET_CART_ERROR_TYPE,
    SET_CART_ERROR_DATA
} from '../store/mutation-types'
import CartStore from './store/cart_store'
import popup from './popup'
import {trans} from './trans'
import storeAddress from '../libraries/store/store_address'

const StoreAddress  = new storeAddress()
class Cart {
    constructor(){
        this.errorCodes = {
            '208009': 'over_stock',
            '208010': 'out_of_stock'
        }
    }

    initAddToCart(el, store, components)
    {
        return new Vue({
            el: el,
            store: store,
            components: components
        });
    }

    sendAddToCart(rootState, payload, success, error)
    {
        axios.post(rootState.appModule.locale_url + '/carts/add-item', payload)
             .then(success)
             .catch(error);
    }

    sendUpdateCart(rootState, payload, success, error)
    {
        axios.post(rootState.appModule.locale_url + '/carts/update-item', payload)
            .then(success)
            .catch(error)
    }

    sendRemoveItem(rootState, payload, success, error)
    {
        axios.post(rootState.appModule.locale_url + '/carts/remove-item', payload)
            .then(success)
            .catch(error)
    }

    addToCartSuccess(response, commit, payload, getters, addToCartData)
    {
        var cartStore = new CartStore();
        commit(SET_CART_ITEMS, response.data.data);

        payload.product.quantity = addToCartData.quantity;
        var addToCartSuccessPayload = {
            'success': true,
            'data': payload.product
        };
        $.toast({
            heading: getters.lang.updated_cart_successful,
            position: 'top-right',
            icon: 'success'
        });
        // commit(SET_ADD_TO_CART_SUCCESS, addToCartSuccessPayload);
        cartStore.update(response.data.data);
    }

    addToCartError(response, commit, getters, payload)
    {
        if (this.isHandleErrorCode(_.get(response, 'data.error_code', 0))) {
            let type = this.getErrorType(_.get(response, 'data.error_code', 0))

            commit(SET_ADD_TO_CART_DATA, payload.product)
            commit(SET_CART_ERROR_TYPE, type)
            commit(SET_CART_ERROR_DATA, response.data)
            commit(SET_ADD_TO_CART_ERROR_LOW_STOCK, true)
        } else {
            if (commit) {
                commit(SET_ADD_TO_CART_SUCCESS_FROM_STORE, false);
            }

            var message = '';
            if (typeof response.data == 'object' && typeof response.data.message == 'string') {
                message = response.data.message;
            }

            popup.open(getters.lang.could_not_add_product_to_cart, message, 'error');
        }
    }

    updateToCartSuccess(response, commit, payload, getters)
    {
        var lang = getters.lang
        commit(SET_CART_ITEMS, response.data.data);
        commit(SET_CART_SUMMARY, response.data.summary);

        $.toast({
            heading: lang.updated_cart_successful,
            position: 'top-right',
            icon: 'success'
        });
    }

    updateToCartError(response, getters)
    {
        var message = '';
        if (typeof response.data == 'object' && typeof response.data.message == 'string') {
            message = response.data.message;
        }

        popup.open(getters.lang.could_not_update_product_in_cart, message, "error", true);
    }

    deleteFromCartSuccess(response, commit, payload, getters)
    {
            commit(SET_CART_ITEMS, response.data.data);
            commit(SET_CART_SUMMARY, response.data.summary);
    }

    deleteFromCartError(response, getters)
    {
        popup.open('', trans('could_not_remove_product_form_cart', {code: response.data.error_code}), 'error', true)
    }

    receiveCartFromApi(getters, commit, success)
    {
        axios.get(getters.locale_url + '/stores/get-current-store').then(response => {
            let data = response.data.data
            let address = StoreAddress.getSelected()
            if (!data.store_id || !data.store_price_id) {
                let storeData = {
                    store_price_id: address.store_price,
                    store_id: address.main_inventory_store
                }
                axios.post(getters.locale_url + '/stores/set-current-store', storeData).then(response => {
                    this.getCart(getters, commit, success)
                })
            }else{
                this.getCart(getters, commit, success)
            }
        }).catch(error => {
            console.log(error)
        })
        
    }

    getCart(getters, commit, success) {
        axios.get(getters.locale_url + '/carts/json')
            .then(function (response) {
                if (response.data.status == 'ok') {
                    commit(SET_CART_ITEMS, response.data.data);
                    if (typeof response.data.summary == 'object' && response.data.summary) {
                        commit(SET_CART_SUMMARY, response.data.summary);
                    }
                }
                if (success) {
                    success();
                }
            })
            .catch(function () {
            })
    }

    isHandleErrorCode(code) {
        code = code + ''
        if (typeof this.errorCodes[code] == 'string') {
            return true
        }
        return false
    }

    getErrorType(code) {
        code = code + ''
        let type = 'out_of_stock'
        if (typeof this.errorCodes[code] == 'string') {
            type = this.errorCodes[code]
        }

        return type
    }

}

export default Cart;