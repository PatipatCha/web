import StoreMenuPicker from '../components/store/MenuPicker'
import store from '../store'
import MakroCartStoreLib from '../libraries/makro_store'
import { SELECT_STORE } from '../store/mutation-types'
import StorePickerProductNotInStore from '../components/store/PickerProductNotInStore'
import ShippingAddressForm from '../components/store/ShippingAddressForm'
import SelectStoreButton from '../components/store/SelectStoreButton'
import SelectAddressModal from  '../components/store/SelectAddressModal'
import ViewMapButton from '../components/store/ViewMapButton'

Vue.component('store-menu-picker', StoreMenuPicker)
Vue.component('store-picker-product-not-in-store', StorePickerProductNotInStore)
Vue.component('select-store-button', SelectStoreButton)
Vue.component('select-address-modal', SelectAddressModal)
Vue.component('shipping-address-form', ShippingAddressForm)
Vue.component('view-map-button', ViewMapButton)

if (store.getters.showStoreSelector == 1) {
    store.dispatch('getStoresFromApi')
}


if (typeof GLOBAL_SETTING.current_store == 'string'
    || typeof GLOBAL_SETTING.current_store == 'number'
    || typeof GLOBAL_SETTING.current_store == 'object')
{
    store.commit(SELECT_STORE, GLOBAL_SETTING.current_store)

}