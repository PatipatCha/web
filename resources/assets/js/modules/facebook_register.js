import store from '../store'
import * as mutationTypes from '../store/mutation-types'
import FacebookRegister from '../components/member/register/Facebook'

Vue.component('facebook-register', FacebookRegister)

if (typeof MEMBER_REGISTER_FACEBOOK_DATA == 'object') {
    store.commit(mutationTypes.UPDATE_FACEBOOK_REGSITER_USERNAME, MEMBER_REGISTER_FACEBOOK_DATA.facebook_user_email)
    store.commit(mutationTypes.UPDATE_FACEBOOK_REGSITER_FACEBOOK_ID, MEMBER_REGISTER_FACEBOOK_DATA.facebook_user_id)
}
