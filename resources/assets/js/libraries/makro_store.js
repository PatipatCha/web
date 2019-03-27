class MakroStore {

    setCurrentStoreToApi(getters, storeId, success, error)
    {
        var param = {
            'store_id': storeId
        };

        axios.post(getters.locale_url + '/stores/set-current-store', param)
            .then(success)
            .catch(error);
    }

    getCurrentStoreFromApi(getters, success, error)
    {
        axios.get(getters.locale_url + '/stores/get-current-store')
            .then(success)
            .catch(error);
    }

}


export default MakroStore