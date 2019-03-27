require('es6-promise').polyfill()

import Vue from 'vue'
import Vuex from 'vuex'
import getters from './getters'
import cartModule from './modules/shopping_cart'
import appModule from './modules/app'
import wishListModule from './modules/wish_list'
import productFilterModule from './modules/product_filter'
import productSorter from './modules/product_sorter'
import productDisplayer from './modules/product_displayer'
import storeModule from './modules/store'
import FacebookLogin from './modules/facebook_login'
import FacebookRegister from './modules/facebook_register'
import ProductDetail from './modules/product_detail'
import Member from './modules/member'

Vue.use(Vuex)

export default new Vuex.Store({
    modules: {
        getters,
        appModule,
        cartModule,
        wishListModule,
        productFilterModule,
        productSorter,
        productDisplayer,
        storeModule,
        FacebookLogin,
        FacebookRegister,
        ProductDetail,
        Member
    }
})

