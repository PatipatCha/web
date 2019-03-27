import * as mutationTypes from '../mutation-types'

//State
const state = {
    error: ''
}

//Mutations
const mutations = {
    [mutationTypes.UPDATE_FACEBOOK_LOGIN_ERROR] (state, error) {
        state.error = error;
    }
}

export default {
    state,
    mutations
}