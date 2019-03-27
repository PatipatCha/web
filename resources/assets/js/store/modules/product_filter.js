import * as types from '../mutation-types'
import ProductFilterSorter from '../../libraries/product_filter_sorter'

function castPrice(price)
{
    return price;
}

const productFilterSorter = new ProductFilterSorter();

const state = {
    prices: {
        min: null,
        max: null
    },
    brands: [],
    category_id: null,
    sizes: [],
    colors: [],
    capacities: [],
    packages: [],
    include_category_filter: true,
    category_filter_name: 'product_category_lv0_id'
}

const mutations = {
    [types.PRODUCT_FILTER_CHANGE_MIN_PRICE] (state, price) {
        state.prices.min = castPrice(price)
    },

    [types.PRODUCT_FILTER_CHANGE_MAX_PRICE] (state, price) {
        state.prices.max = castPrice(price)
    },

    [types.SELECT_BRAND_FILTER] (state, name) {
        state.brands.push(name);
    },

    [types.SET_BRAND_FILTER] (state, brands) {
        state.brands = brands;
    },

    [types.UNSELECT_BRAND_FILTER] (state, index) {
        state.brands.splice(index, 1)
    },

    [types.SELECT_CATEGORY_FILTER] (state, id) {
        state.category_id = id
    },

    [types.SET_INCLUDE_CATEGORY_FILTER] (state, value) {
        state.include_category_filter = value
    },
    [types.SELECT_SIZE_FILTER] (state, value) {
        state.sizes.push(value)
    },
    [types.UNSELECT_SIZE_FILTER] (state, index) {
        state.sizes.splice(index, 1)
    },
    [types.SET_SIZE_FILTER] (state, sizes) {
        state.sizes = sizes
    },
    [types.SELECT_COLOR_FILTER] (state, value) {
        state.colors.push(value)
    },
    [types.UNSELECT_COLOR_FILTER] (state, index) {
        state.colors.splice(index, 1)
    },
    [types.SET_COLOR_FILTER] (state, colors) {
        state.colors = colors
    },
    [types.SELECT_CAPACITY_FILTER] (state, value) {
        state.capacities.push(value)
    },
    [types.UNSELECT_CAPACITY_FILTER] (state, index) {
        state.capacities.splice(index, 1)
    },
    [types.SET_CAPACITY_FILTER] (state, capacities) {
        state.capacities = capacities
    },
    [types.SELECT_PACKAGE_FILTER] (state, value) {
        console.log(state.packages)
        state.packages.push(value)
    },
    [types.UNSELECT_PACKAGE_FILTER] (state, index) {
        state.packages.splice(index, 1)
    },
    [types.SET_PACKAGE_FILTER] (state, packages) {
        state.packages = packages
    },
    [types.SET_CATEGORY_FILTER_NAME] (state, name) {
        state.category_filter_name = name
    }
}

const getters = {
    productFilterStatus: state => {
        var minPrice = parseFloat(state.prices.min)
        var maxPrice = parseFloat(state.prices.max)
        var data = []
        if (!isNaN(minPrice) && minPrice >=0) {
            data.push({'filters[min_price]': minPrice})
        }
        if (!isNaN(maxPrice) && maxPrice >=0) {
            data.push({'filters[max_price]': maxPrice})
        }

        return data;
    },
    productFilterMinPrice: state => state.prices.min,
    productFilterMaxPrice: state => state.prices.max,
    productFilterBrands: state => {
        if (state.brands.length > 0) {
            const data = [
                {'filters[brand]': state.brands.join(';')}
            ];

            return data;
        }

        return 0;
    },
    productFilterCategories: state => {
        if (state.category_id && state.include_category_filter) {
            var filter = {};
            filter['filters[' + state.category_filter_name + ']'] = state.category_id
            const data = [
                filter
            ];
            return data;
        }

        return false;
    },
    productFilterSizes: state => {
        if (state.sizes.length > 0) {
            const data = [
                {'filters[size]': state.sizes.join(';')}
            ];

            return data;
        }

        return 0;
    },
    productFilterColors: state => {
        if (state.colors.length > 0) {
            const data = [
                {'filters[color]': state.colors.join(';')}
            ];

            return data;
        }

        return 0;
    },
    productFilterCapacities: state => {
        if (state.capacities.length > 0) {
            const data = [
                {'filters[capacity]': state.capacities.join(';')}
            ];

            return data;
        }

        return 0;
    },
    productFilterPackages: state => {
        if (state.packages.length > 0) {
            const data = [
                {'filters[package]': state.packages.join(';')}
            ];

            return data;
        }

        return 0;
    },
}

const actions = {
    filterPricesChange ({commit, getters, rootState}, payload) {
        commit(types.PRODUCT_FILTER_CHANGE_MIN_PRICE, payload.min);
        commit(types.PRODUCT_FILTER_CHANGE_MAX_PRICE, payload.max);

        productFilterSorter.onChange(rootState, getters);
    },

    filterSelectBrand ({state, commit, getters, rootState}, payload) {
        payload.name = payload.name + '';
        var index = state.brands.indexOf(decodeURIComponent(payload.name));

        if (index != -1) {
            commit(types.UNSELECT_BRAND_FILTER, index)
        } else {
            commit(types.SELECT_BRAND_FILTER, payload.name)
        }

        setTimeout(function () {
            productFilterSorter.onChange(rootState, getters);
        }, 1);

    },

    selectFilterCategory({state, commit, getters, rootState}, payload) {
        commit(types.SELECT_CATEGORY_FILTER, payload.id);
        commit(types.SET_CATEGORY_FILTER_NAME, payload.name)

        setTimeout(function () {
            productFilterSorter.onChange(rootState, getters, 'productFilterCategories');
        }, 1);
    },

    filterSelectSize ({state, commit, getters, rootState}, payload) {
        payload.name = payload.name + '';
        var index = state.sizes.indexOf(decodeURIComponent(payload.name));
        if (index != -1) {
            commit(types.UNSELECT_SIZE_FILTER, index)
        } else {
            commit(types.SELECT_SIZE_FILTER, payload.name)
        }

        setTimeout(function () {
            productFilterSorter.onChange(rootState, getters);
        }, 1);

    },

    filterSelectColor ({state, commit, getters, rootState}, payload) {
        payload.name = payload.name + '';
        var index = state.colors.indexOf(decodeURIComponent(payload.name));
        if (index != -1) {
            commit(types.UNSELECT_COLOR_FILTER, index)
        } else {
            commit(types.SELECT_COLOR_FILTER, payload.name)
        }

        setTimeout(function () {
            productFilterSorter.onChange(rootState, getters);
        }, 1);

    },


    filterSelectCapacity ({state, commit, getters, rootState}, payload) {
        payload.name = payload.name + '';
        var index = state.capacities.indexOf(decodeURIComponent(payload.name));

        if (index != -1) {
            commit(types.UNSELECT_CAPACITY_FILTER, index)
        } else {
            commit(types.SELECT_CAPACITY_FILTER, payload.name)
        }

        setTimeout(function () {
            productFilterSorter.onChange(rootState, getters);
        }, 1);

    },

    filterSelectPackage ({state, commit, getters, rootState}, payload) {
        payload.name = payload.name + '';
        var index = state.packages.indexOf(decodeURIComponent(payload.name));

        if (index != -1) {
            commit(types.UNSELECT_PACKAGE_FILTER, index)
        } else {
            commit(types.SELECT_PACKAGE_FILTER, payload.name)
        }

        setTimeout(function () {
            productFilterSorter.onChange(rootState, getters);
        }, 1);

    },

}

export default {
    state,
    mutations,
    getters,
    actions
}