import StoreManager from '../store_manager/store_manager'
import * as keys from '../store_manager/keys'

class CartStore {
    constructor () {
        this.storeManager = new StoreManager()
        this.keys = keys;
    }

    update (items) {
        this.storeManager.set(this.keys.CART, items)
    }

    get () {
        let items = this.storeManager.get(this.keys.CART);

        if (!items) {
            items = []
        }

        return items
    }

    emptyCart () {
        this.storeManager.set(this.keys.CART, [])
    }
}


export default CartStore