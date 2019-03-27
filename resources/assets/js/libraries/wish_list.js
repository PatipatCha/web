import { UPDATE_WISH_LIST_ITEMS } from '../store/mutation-types'
import popup from './popup'
import {trans} from "./trans";

class WishList
{
    add(getters, payload, success, error)
    {
        axios.post(getters.locale_url + '/favorites/add-item', payload)
            .then(success)
            .catch(error);
    }

    remove(getters, payload, success, error)
    {
        axios.post(getters.locale_url + '/favorites/remove-item', payload)
            .then(success)
            .catch(error);
    }

    onAddSuccess(response, commit)
    {
        commit(UPDATE_WISH_LIST_ITEMS, response.data.data)

        // $.toast({
        //     heading: 'Added product to wish list.',
        //     position: 'top-right',
        //     icon: 'success'
        // });
    }

    onAddError(response, commit, getters)
    {
        var message = '';
        if (typeof response.data == 'object' && typeof response.data.message == 'string') {
            message = response.data.message;
        }

        popup.open('', trans('could_not_add_product_to_wish_list', {code: response.data.error_code}), 'error', true)
    }

    onRemoveSuccess(response, commit)
    {
        commit(UPDATE_WISH_LIST_ITEMS, response.data.data)

        // $.toast({
        //     heading: 'Removed product from wish list.',
        //     position: 'top-right',
        //     icon: 'warning'
        // });
    }

    onRemoveError(response, commit, getters)
    {
        var message = '';
        if (typeof response.data == 'object' && typeof response.data.message == 'string') {
            message = response.data.message;
        }

        popup.open('', trans('could_not_remove_product_form_wish_list', {code: response.data.error_code}), 'error', true)
    }
}

export default WishList