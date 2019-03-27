import StoreManager from '../store_manager/store_manager'
import * as keys  from '../store_manager/keys'


class MakroStore {
    constructor () {
        this.storeManager = new StoreManager()
    }

    set (storeId) {
        this.storeManager.set(keys.CURRENT_STORE, parseInt(storeId))
    }

    get () {
        return this.storeManager.get(keys.CURRENT_STORE)
    }
}

export default MakroStore