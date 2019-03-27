import * as types from '../mutation-types'
import Cart from '../../libraries/cart'
import CartStore from '../../libraries/store/cart_store'
import 'jquery-toast-plugin/dist/jquery.toast.min.js'
import 'jquery-toast-plugin/dist/jquery.toast.min.css'
import popup from '../../libraries/popup'
import GoogleAnanlytic from '../../libraries/google_analytic'

const cartStore = new CartStore();
const googleAnalytic = new GoogleAnanlytic()

//State
const state = {
    items: cartStore.get(),
    summary: {
        grand_total: 0,
        product_fee: 0,
        sale_tax: 0,
        delivery_fee: 0,
        sub_total: 0
    },
    addToCartSuccess: false,
    addToCartData: null,
    addToCartSuccessFromSelectStore: false,
    addToCartErrorLowStock: false,
    errorType: 'out_of_stock', //'out_of_stock', 'over_stock'
    errorData: null,
    reOrder: null,
    reOrderPopup: false
}

//Mutations
const mutations = {
    [types.ADD_TO_CART](state, payload) {
        state.items.push(payload);
    },

    [types.UPDATE_CART_ITEM_QTY](state, payload) {
        state.items[payload.index].qty = payload.qty;
    },

    [types.EMPTY_CART](state) {
        state.items = [state.items]
        state.items.length = 0

    },
    [types.SET_CART_ITEMS](state, items) {
        state.items.length = 0;
        state.items = items;
        cartStore.update(items);
    },
    [types.SET_CART_SUMMARY](state, summary) {
        state.summary = summary
    },
    [types.SET_ADD_TO_CART_SUCCESS](state, payload) {
        state.addToCartSuccess = payload.success;
        state.addToCartData = payload.data;
    },
    [types.SET_ADD_TO_CART_SUCCESS_FROM_STORE](state, status) {
        state.addToCartSuccessFromSelectStore = status;
    },
    [types.SET_ADD_TO_CART_ERROR_LOW_STOCK](state, status) {
        state.addToCartErrorLowStock = status
    },
    [types.SET_ADD_TO_CART_DATA](state, payload) {
        state.addToCartData = payload
    },
    [types.SET_CART_ERROR_TYPE](state, type) {
        state.errorType = type
    },
    [types.SET_CART_ERROR_DATA](state, data) {
        state.errorData = data
    },
    [types.SET_RE_ORDER_DATA](state, data) {
        state.reOrder = data
    },
    [types.SET_RE_ORDER_POPUP](state, status) {
    state.reOrderPopup = status
}
}

//Getters
const getters = {
    cartCount: state => {
        var count = 0;
        for (var i = 0; i < state.items.length; ++i) {
            count += state.items[i].items.length;
        }

        return count;
    },

    grandTotal: state => state.summary.grand_total,
    addToCartSuccess: state => state.addToCartSuccess,
    addToCartData: state => state.addToCartData,
    addToCartQuantity: state => {
        if (state.addToCartData) {
            return state.addToCartData.quantity
        }

        return 0
    },
    reOrder: state => state.reOrder
}

//Actions
const actions = {
    addToCart({state, commit, getters, rootState, dispatch}, payload) {
        var cart = new Cart();
        const data = {
            'store_id': getters.currentStore,
            'content_id': payload.product.id,
            'quantity': payload.qty
        };
        cart.sendAddToCart(rootState, data, function (response) {
            payload.callback();

            if (typeof response.data == 'object') {
                if (response.data.status == 'ok') {
                    //Success
                    cart.addToCartSuccess(response, commit, payload, getters, data);
                    googleAnalytic.addToCart(payload.product, payload.qty)
                } else {
                    //Error
                    cart.addToCartError(response, commit, getters, payload);
                    // if (response.data.error_code == 208009) {
                    //
                    //     commit(types.SET_ADD_TO_CART_DATA, payload.product)
                    //     commit(types.SET_ADD_TO_CART_ERROR_LOW_STOCK, true)
                    // } else {
                    //     cart.addToCartError(response, commit, getters);
                    // }
                }
            } else {
                //Error
                cart.addToCartError(response, commit, getters, payload);
            }
        }, function (response) {
            payload.callback();

            //Error
            cart.addToCartError(response, commit, getters, payload);
        });
    },

    clearCart({state, rootState, commit}) {
        commit(types.EMPTY_CART, '')
        cartStore.emptyCart()
    },

    incrementCartItem({state, commit, getters, rootState}, payload) {
        console.log('inc...')
        var cart = new Cart();
        var item = payload.item;
        const data = {
            'store_id': getters.currentStore,
            'id': item.id,
            'quantity': item.quantity + 1
        };

        cart.sendUpdateCart(rootState, data, function (response) {

            payload.callback();
            payload.item.content.data.price = _.get(payload, 'item.price.data.price')
            payload.item.content.data.normal_price = _.get(payload, 'item.price.data.normal_price')
            payload.product = payload.item.content.data

            if (typeof response.data == 'object') {
                if (response.data.status == 'ok') {
                    //Success
                    cart.updateToCartSuccess(response, commit, payload, getters, data);
                    googleAnalytic.addToCart(item.content.data, parseInt(item.quantity))
                } else {
                    console.log(payload)
                    //Error
                    cart.addToCartError(response, commit, getters, payload);
                }
            } else {
                //Error
                cart.addToCartError(response, commit, getters, payload);
            }
        }, function (response) {

            payload.callback();
            //Error
            cart.updateToCartError(response, getters);
        });
    },

    decrementCartItem({state, commit, getters, rootState}, payload) {
        var cart = new Cart()

        var item = payload.item;
        const data = {
            'store_id': getters.currentStore,
            'id': item.id,
            'quantity': item.quantity - 1
        };

        cart.sendUpdateCart(rootState, data, function (response) {
            payload.callback();

            payload.item.content.data.price = _.get(payload, 'item.price.data.price')
            payload.item.content.data.normal_price = _.get(payload, 'item.price.data.normal_price')
            payload.product = payload.item.content.data

            if (typeof response.data == 'object') {
                if (response.data.status == 'ok') {
                    //Success
                    cart.updateToCartSuccess(response, commit, payload, getters, data);
                    googleAnalytic.addToCart(item.content.data, parseInt(item.quantity))
                } else {
                    console.log(payload)
                    //Error
                    cart.addToCartError(response, commit, getters, payload);
                }
            } else {
                //Error
                cart.addToCartError(response, commit, getters, payload);
            }
        }, function (response) {
            //Error
            payload.callback();
            cart.updateToCartError(response, getters);
        });
    },

    updateCartItem({state, commit, getters, rootState}, payload) {
        var cart = new Cart()

        console.log(payload)

        var item = payload.item;
        const data = {
            'store_id': getters.currentStore,
            'id': item.id,
            'quantity': parseInt(payload.amount)
        };

        cart.sendUpdateCart(rootState, data, function (response) {
            payload.callback();

            payload.item.content.data.price = _.get(payload, 'item.price.data.price')
            payload.item.content.data.normal_price = _.get(payload, 'item.price.data.normal_price')
            payload.product = payload.item.content.data

            if (typeof response.data == 'object') {
                if (response.data.status == 'ok') {
                    //Success
                    cart.updateToCartSuccess(response, commit, payload, getters, data);
                    googleAnalytic.addToCart(item.content.data, parseInt(item.quantity))
                } else {
                    //Error
                    if (payload.error) {
                        payload.error()
                    }
                    cart.addToCartError(response, commit, getters, payload);
                }
            } else {
                //Error
                if (payload.error) {
                    payload.error()
                }
                cart.addToCartError(response, commit, getters, payload);
            }

            // if (typeof response.data == 'object') {
            //     if (response.data.status == 'ok') {
            //         //Success
            //         cart.updateToCartSuccess(response, commit, item, getters);
            //         if (item.content && item.content.data) {
            //             googleAnalytic.addToCart(item.content.data, parseInt(item.quantity))
            //         }
            //
            //     } else {
            //         //Error
            //         cart.updateToCartError(response, getters);
            //     }
            // } else {
            //     //Error
            //     cart.updateToCartError(response, getters);
            // }
        }, function (response) {
            //Error
            payload.callback();

            if (payload.error) {
                payload.error()
            }

            cart.addToCartError(response, commit, getters, payload);
        });
    },

    removeCartItem({state, commit, getters, rootState}, payload) {
        var lang = getters.lang;
        popup.open('', lang.are_you_sure_to_delete_this_item_form_your_cart, 'confirm', true, function (loadingDone) {
            //Confirm
            var cart = new Cart()

            var item = payload
            if (typeof payload.item == 'object') {
                var item = payload.item
            }


            const data = {
                'store_id': getters.currentStore,
                'id': item.id
            };

            cart.sendRemoveItem(rootState, data, function (response) {
                loadingDone()
                popup.close()
                if (typeof response.data == 'object') {
                    if (response.data.status == 'ok') {
                        //Success
                        cart.deleteFromCartSuccess(response, commit, item, getters);
                        if (item.content && item.content.data) {
                            googleAnalytic.removeFromCart(item.content.data, 0)
                        }

                    } else {
                        //Error
                        cart.deleteFromCartError(response, getters);
                    }
                } else {
                    //Error
                    cart.deleteFromCartError(response, getters);
                }
            }, function (response) {
                //Error
                loadingDone()
                popup.close()
                cart.deleteFromCartError(response, getters);
            });
        }, function () {
            //Cancel
            if (payload.cancel_callback) {
                payload.cancel_callback()
            }
        })
    },

    receiveCartFromApi({state, commit, getters, rootState}, payload) {
        if (GLOBAL_SETTING.route_name != 'carts.index'
            && GLOBAL_SETTING.route_name != 'carts.checkout'
            && GLOBAL_SETTING.route_name != 'carts.shipping'
            && GLOBAL_SETTING.route_name != 'carts.payment.success'
        ) {
            var cart = new Cart()
            cart.receiveCartFromApi(getters, commit);
        } else {

        }
    }
}

const cartModule = {
    state,
    mutations,
    getters,
    actions

};

export default cartModule