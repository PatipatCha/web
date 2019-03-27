import * as types from '../mutation-types'
import ProductDisplayer from '../../libraries/store/product_displayer_store'

const displayTypes = {'grid': 'grid', 'list': 'list'}
const productDisplayer = new ProductDisplayer()
var type = productDisplayer.get();
if (typeof displayTypes[type] != 'string') {
    type =  displayTypes.grid
}

const state = {
    type: type, //'grid', 'list'
}

const mutations = {
    [types.PRODUCT_DISPLAYER_CHANGE_TYPE] (state, type) {
        if (typeof displayTypes[type] != 'string') {
            throw 'Product displayer not supprt type \'' + type + '\''
        }

        state.type = type;
    }
}

const getters = {
    productDisplayerType: state => {
        var type = productDisplayer.get();
        if (typeof displayTypes[type] != 'string') {
            return displayTypes.list
        }

        return type;
    }
}

const actions = {
    changeProductDisplayer ({state, commit}, type) {
        commit(types.PRODUCT_DISPLAYER_CHANGE_TYPE, type)
        productDisplayer.set(type)
    }
}

export default {
    state,
    mutations,
    getters,
    actions
}