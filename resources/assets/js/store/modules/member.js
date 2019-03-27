import { SET_SHIPPING_ADDRESS, SET_SHIPPING_DISPLAY, SET_SHIPPING_SELECT } from '../mutation-types'

const state = {
    shipping_address: [],  
    shipping_display: {},
    shipping_select: ''
}

const mutations = {
    [SET_SHIPPING_ADDRESS] (state, shippings) {
        state.shipping_address = shippings;
    },

    [SET_SHIPPING_DISPLAY] (state, display) {
        state.shipping_display = display;
    },

    [SET_SHIPPING_SELECT] (state, select) {
        state.shipping_select = select;
    },
}

export default {
    state,
    mutations
}