import StoreManager from '../store_manager/store_manager'
import * as keys from '../store_manager/keys'

class WishListStore {

    constructor () {
        this.storeManager = new StoreManager()
    }

    update (items) {
        this.storeManager.set(keys.WISH_LIST, items)
    }

    get () {
        let items =  this.storeManager.get(keys.WISH_LIST)

        if (!items) {
            items = []
        }

        return items
    }

}

export default WishListStore