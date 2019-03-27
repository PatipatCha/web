import RelatedProducts from '../components/product/RelatedProducts'
import RecentProducts from '../components/product/RecentProducts'
import SearchProducts from '../components/product/SearchProducts'

import store from '../store'

Vue.component('related-products', RelatedProducts)
Vue.component('recent-products', RecentProducts)
Vue.component('search-products', SearchProducts)

if(typeof GLOBAL_SETTING.product_id == 'string' && GLOBAL_SETTING.product_id.length > 0){
    var productId = GLOBAL_SETTING.product_id;
    axios.get(store.state.appModule.locale_url + '/product/'+productId+'/increate-view')
    .then((response) => {
        console.log(response.data)
    })
    .catch((error) => {

    })
}