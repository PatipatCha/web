import Register from '../components/member/register/Register'
import FacebookLogin from '../components/member/login/Facebook'
import store from '../store'
import * as mutationTypes from '../store/mutation-types'

Vue.component('register', Register)
Vue.component('facebook-login', FacebookLogin)

if (typeof MEMBER_DATA  == 'object' && typeof MEMBER_DATA.facebook_error == 'string') {

}
//store.commit(mutationTypes.UPDATE_FACEBOOK_LOGIN_ERROR, MEMBER_DATA.facebook_error)

