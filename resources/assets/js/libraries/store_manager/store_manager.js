import * as allowKeys from './keys'
import localStorage from 'store'

import cookieStorageEngine from 'store/storages/cookieStorage'
import engine from 'store/src/store-engine'
import plugins from 'store/plugins/json2'

const cookieStorage = engine.createStore(cookieStorageEngine, plugins)

class StoreManager {

    isAllowedKey (key) {
        if (typeof allowKeys[key] != 'string') {
            return false;
        }

        return true;
    }

    set (key, value) {
        if (this.isAllowedKey(key)) {
            localStorage.set(key, value)
        } else {
            //throw 'Key \'' + key + '\' is not allowed'
        }

        if (key == 'SELECTED_ADDRESS') {
            cookieStorage.set(key, value)
        }
    }

    get (key) {
        if (this.isAllowedKey(key)) {
            return localStorage.get(key)
        } else {
            //throw 'Key \'' + key + '\' is not allowed'
        }
    }

}

export default StoreManager