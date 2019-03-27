import Vue from 'vue'
import * as mutationTypes from '../store/mutation-types'
import store from '../store'
import Switcher from '../components/product/displayer/Switcher'
import AddToCartButton from '../components/cart/AddToCartButton'
import ProductPriceFilter from '../components/product/filter/PriceFilter'
import ProductSorter from '../components/product/sorter/Sorter'
import ProductBrandFilter from '../components/product/filter/BrandFilter'
import ProductCategoryFilter from '../components/product/filter/CategoryFilter'
import ProductFilter from '../components/product/filter/Filter'
import FilterColor from '../components/product/filter/FilterColor'

//Set initial filter and sorter

//Product price filter

if (typeof GLOBAL_SETTING.modules.product.inputs.filters == 'object' && typeof GLOBAL_SETTING.modules.product.inputs.filters.min_price == 'string') {
    store.commit(mutationTypes.PRODUCT_FILTER_CHANGE_MIN_PRICE, parseFloat(GLOBAL_SETTING.modules.product.inputs.filters.min_price))
}
if (typeof GLOBAL_SETTING.modules.product.inputs.filters == 'object' && typeof GLOBAL_SETTING.modules.product.inputs.filters.max_price == 'string') {
    store.commit(mutationTypes.PRODUCT_FILTER_CHANGE_MAX_PRICE, parseFloat(GLOBAL_SETTING.modules.product.inputs.filters.max_price))

}

//Product sorter
if (typeof GLOBAL_SETTING.modules.product.inputs.sorter_field == 'string'
    && typeof GLOBAL_SETTING.modules.product.inputs.sorter_direction)
{
    store.dispatch('initSortProduct', {
        'sorter': GLOBAL_SETTING.modules.product.inputs.sorter_field,
        'direction': GLOBAL_SETTING.modules.product.inputs.sorter_direction
    })
}

//Product  brand filter
if (typeof GLOBAL_SETTING.modules.product.inputs.filters == 'object' && typeof GLOBAL_SETTING.modules.product.inputs.filters.brand == 'string') {
    var brands = GLOBAL_SETTING.modules.product.inputs.filters.brand.split(';');
    store.commit(mutationTypes.SET_BRAND_FILTER, brands);
}


//Product category filter
if (typeof GLOBAL_SETTING.modules.product.inputs.filters == 'object'
    && (
        typeof GLOBAL_SETTING.modules.product.inputs.filters.product_category_lv0_id == 'string'
        || typeof GLOBAL_SETTING.modules.product.inputs.filters.product_category_lv1_id == 'string'
        || typeof GLOBAL_SETTING.modules.product.inputs.filters.product_category_lv2_id == 'string'
    )
) {
    var categoryId = 0;
    for (var i = 0; i <= 2; ++i) {
        categoryId = GLOBAL_SETTING.modules.product.inputs.filters['product_category_lv' + i + '_id'];
        if (typeof categoryId == 'string') {
            break
        }
    }

    store.commit(mutationTypes.SELECT_CATEGORY_FILTER, parseInt(categoryId));

    //Set category filter name
    let categoryFilterName = '';
    if (typeof GLOBAL_SETTING.modules.product.inputs.filters.product_category_lv0_id == 'string') {
        categoryFilterName = 'product_category_lv0_id'
    } else if (typeof GLOBAL_SETTING.modules.product.inputs.filters.product_category_lv1_id == 'string') {
        categoryFilterName = 'product_category_lv1_id'
    } else {
        categoryFilterName = 'product_category_lv2_id'
    }

    store.commit(mutationTypes.SET_CATEGORY_FILTER_NAME, categoryFilterName);

} else if (typeof GLOBAL_SETTING.direct_category_id == 'number') {
    if (GLOBAL_SETTING.direct_category_id > 0) {
        store.commit(mutationTypes.SET_INCLUDE_CATEGORY_FILTER, false)
    }

    store.commit(mutationTypes.SELECT_CATEGORY_FILTER, parseInt(GLOBAL_SETTING.direct_category_id))
}

//Product  size filter
if (typeof GLOBAL_SETTING.modules.product.inputs.filters == 'object' && typeof GLOBAL_SETTING.modules.product.inputs.filters.size == 'string') {
    var sizes = GLOBAL_SETTING.modules.product.inputs.filters.size.split(';');
    store.commit(mutationTypes.SET_SIZE_FILTER, sizes);
}

//Product  color filter
if (typeof GLOBAL_SETTING.modules.product.inputs.filters == 'object' && typeof GLOBAL_SETTING.modules.product.inputs.filters.color == 'string') {
    var colors = GLOBAL_SETTING.modules.product.inputs.filters.color.split(';');
    store.commit(mutationTypes.SET_COLOR_FILTER, colors);
}

//Product  capacity filter
if (typeof GLOBAL_SETTING.modules.product.inputs.filters == 'object' && typeof GLOBAL_SETTING.modules.product.inputs.filters.capacity == 'string') {
    var capacities = GLOBAL_SETTING.modules.product.inputs.filters.capacity.split(';');
    store.commit(mutationTypes.SET_CAPACITY_FILTER, capacities);
}

//Product  package filter
if (typeof GLOBAL_SETTING.modules.product.inputs.filters == 'object' && typeof GLOBAL_SETTING.modules.product.inputs.filters.package == 'string') {
    var packages = GLOBAL_SETTING.modules.product.inputs.filters.package.split(';');
    store.commit(mutationTypes.SET_PACKAGE_FILTER, packages);
}



Vue.component('product-displayer-switcher', Switcher)
Vue.component('add-to-cart-button', AddToCartButton)
Vue.component('product-price-filter', ProductPriceFilter)
Vue.component('product-sorter', ProductSorter)
Vue.component('product-brand-filter', ProductBrandFilter)
Vue.component('product-category-filter', ProductCategoryFilter)
Vue.component('product-filter', ProductFilter)
Vue.component('product-filter-color', FilterColor)


