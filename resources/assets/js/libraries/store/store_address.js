import StoreManager from '../store_manager/store_manager'
import * as keys from '../store_manager/keys'
import { get } from 'lodash'


class StoreAddress {
    constructor() {
        this.storeManager = new StoreManager()
    }

    set(payload) {
        console.log(keys.STORE_ADDRESSES)
        this.storeManager.set(keys.STORE_ADDRESSES, payload)
    }

    get() {
        if (this.storeManager.get(keys.STORE_ADDRESSES)) {
            let addresses = this.storeManager.get(keys.STORE_ADDRESSES)
            const selectedAddress = this.getSelected()
            let index = addresses.findIndex((item) => {
                return item.id == get(selectedAddress, 'id')
            })

            if (index > 0) {
                const address = addresses[index]
                addresses.splice(index, 1)
                addresses = [
                    address,
                    ...addresses
                ]

                this.set(addresses)
            }

            return addresses

        } else {
            return []
        }
    }

    setSelected(address) {
        this.storeManager.set(keys.SELECTED_ADDRESS, address)
    }

    getSelected() {
        if (this.storeManager.get(keys.SELECTED_ADDRESS)) {
            return this.storeManager.get(keys.SELECTED_ADDRESS)
        } else {
            return null
        }
    }

    addAddress(address) {
        let addresses = this.get()
        addresses.unshift(address)

        this.set(addresses)
    }

    setOpenPopup(status) {
        this.storeManager.set(keys.OPEN_POPUP_ADDRESS, status)
    }

    getOpenPopup() {
        if (this.storeManager.get(keys.OPEN_POPUP_ADDRESS)) {
            return this.storeManager.get(keys.OPEN_POPUP_ADDRESS)
        } else {
            return false
        }
    }

    updateAddress(payload) {
        let addresses = this.get()
        addresses[payload.index] = payload.data
        this.set(addresses)
    }
}

export default StoreAddress