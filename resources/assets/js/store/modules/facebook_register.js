import * as mutationTypes from '../mutation-types'

const state = {
    username: '',
    facebook_user_id: ''
}

const mutations = {
    [mutationTypes.UPDATE_FACEBOOK_REGSITER_USERNAME] (state, username) {
        state.username = username;
    },

    [mutationTypes.UPDATE_FACEBOOK_REGSITER_FACEBOOK_ID] (state, id) {
        state.facebook_user_id = id;
    },
}


export default {
    state,
    mutations
}