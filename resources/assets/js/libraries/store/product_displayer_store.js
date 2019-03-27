import StoreManager from '../store_manager/store_manager'
import * as keys from '../store_manager/keys'

class ProductDisplayerStore {

    constructor () {
        this.storeManager = new StoreManager()
        this.keys = keys;
    }

    set (type) {
        this.storeManager.set(this.keys.PRODUCT_DISPLAYER, type)
    }

    get () {
        return this.storeManager.get(this.keys.PRODUCT_DISPLAYER)
    }

}

export default ProductDisplayerStore