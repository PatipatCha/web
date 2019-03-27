import * as types from '../mutation-types'
import ProductFilterSorter from '../../libraries/product_filter_sorter'
const productFilterSorter = new ProductFilterSorter();

const sorterMapper = {
    best_match: types.PRODUCT_SORTER_CHANGE_BEST_MATCH,
    order: types.PRODUCT_SORTER_CHANGE_ORDER,
    newest: types.PRODUCT_SORTER_SORT_NEWEST,
    price: types.PRODUCT_SORTER_SORT_PRICE
}

const state = {
    sorters: {
        best_match: null,
        order: null,
        newest: null,
        price_asc: null,
        price_desc: null,
        field_name: 'best_match',
        direction: 'asc'
    },
    current_sorter: null,
    current_sorter_direction: null
}

const mutations = {
    [types.PRODUCT_SORTER_CHANGE_BEST_MATCH] (state, direction) {
        state.sorters.best_match = direction
    },

    [types.PRODUCT_SORTER_CHANGE_ORDER] (state, direction) {
        state.sorters.order = direction
    },

    [types.PRODUCT_SORTER_SORT_NEWEST] (state, direction) {
        state.sorters.newest = direction
    },

    [types.PRODUCT_SORTER_SORT_PRICE] (state, direction) {
        state.sorters.price = direction
    },

    [types.PRODUCT_SORTER_UPDATE_CURRENT_SORTER] (state, direction) {
        state.current_sorter = direction
    },

    [types.PRODUCT_SORTER_UPDATE_CURRENT_SORTER_DIRECTION] (state, direction) {
        state.current_sorter_direction = direction
    },

    [types.PRODUCT_SORTER_UNSET_ALL_SORTER] (state) {
        for (var i in state.sorters) {
            state.sorters[i] = null;
        }
    },
    [types.PRODUCT_SORTER_FIELD_NAME] (state, fieldName) {
        state.sorters.field_name = fieldName
    },
    [types.PRODUCT_SORTER_DIRECTION] (state, direction) {
        state.sorters.direction = direction
    }
}

const getters = {
    sorterQueryString: state => {
        var data = null
        if (state.sorters.field_name != 'best_match') {
            data = [
                {'sorter_field': state.sorters.field_name},
                {'sorter_direction': state.sorters.direction}
            ]
        }

        return data
    }
}


const actions = {
    sortProduct ({state, commit, rootState, getters}, payload) {
        commit(types.PRODUCT_SORTER_FIELD_NAME, payload.field_name)
        commit(types.PRODUCT_SORTER_DIRECTION, payload.direction)

        productFilterSorter.onChange(rootState, getters)

    },

    initSortProduct ({state, commit}, payload) {
        commit(types.PRODUCT_SORTER_FIELD_NAME, payload.sorter)
        commit(types.PRODUCT_SORTER_DIRECTION, payload.direction)
    }
}

export default {
    state,
    mutations,
    getters,
    actions
}