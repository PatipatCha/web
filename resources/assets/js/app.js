require('./bootstrap')

import store from './store'
import './filters'
import * as mutationTypes from './store/mutation-types'
import swal from 'sweetalert'
import 'sweetalert/dist/sweetalert.css'
import popup from './libraries/popup'
import 'select2'
import 'select2/dist/css/select2.css'
import 'select2-bootstrap-theme/dist/select2-bootstrap.css'
import {trans} from "./libraries/trans";
import StoreManager from './libraries/store_manager/store_manager'

const storeManager = new StoreManager()

//Initial
//sharedStore = store
store.commit(mutationTypes.SET_URL, GLOBAL_SETTING.url)
store.commit(mutationTypes.SET_LOCALE_URL, GLOBAL_SETTING.locale_url)
store.commit(mutationTypes.SET_PRODUCT_LIST_URL, GLOBAL_SETTING.product_list_url)
store.commit(mutationTypes.SET_LOGGED_IN, GLOBAL_SETTING.logged_in)
store.commit(mutationTypes.SET_RECAPTCHA_SITE_KEY, GLOBAL_SETTING.reCaptchaSiteKey)

//Set global lang
if (typeof GLOBAL_SETTING.lang == 'object') {
    store.commit(mutationTypes.SET_LANG, GLOBAL_SETTING.lang)
}


//Set show store selector
if (typeof GLOBAL_SETTING.show_store_selector == 'number') {
    store.commit(mutationTypes.SET_SHOW_STORE_SELECTOR, GLOBAL_SETTING.show_store_selector)
}

//Set is accept delivery
if (typeof GLOBAL_SETTING.is_accept_delivery == 'number') {
    store.commit(mutationTypes.SET_IS_ACCEPT_DELIVERY, GLOBAL_SETTING.is_accept_delivery)
}

//--------------- Modules ------------------------//
require('./modules/global')
require('./modules/cart')
require('./modules/wish_list')
require('./modules/product_list')
require('./modules/product_detail')
require('./modules/store')
require('./modules/register')
require('./modules/facebook_register')
require('./modules/login')
require('./modules/checkout')
require('./modules/subscribe')
require('./modules/member')
require('./modules/category')
require('./modules/google_analytic')

//Set current locale
if (typeof GLOBAL_SETTING.locale == 'string') {
    store.commit(mutationTypes.SET_LOCALE, GLOBAL_SETTING.locale)
}

//Set addresses
//Provinces
if (typeof GLOBAL_SETTING.provinces == 'object') {
    store.commit(mutationTypes.SET_PROVINCE, GLOBAL_SETTING.provinces)
}

//Cities
if (typeof GLOBAL_SETTING.cities == 'object') {
    store.commit(mutationTypes.SET_CITY, GLOBAL_SETTING.cities)
}

//Sub-Districts
if (typeof GLOBAL_SETTING.sub_districts == 'object') {
    store.commit(mutationTypes.SET_SUB_DISTRICT, GLOBAL_SETTING.sub_districts)
}

//Pickup time
if (typeof GLOBAL_SETTING.pickup_times == 'string') {
    store.commit(mutationTypes.SET_PICKUP_TIME, GLOBAL_SETTING.pickup_times)
}

//Set confirm change store
if (typeof GLOBAL_SETTING.confirm_change_store == 'number') {
    store.commit(mutationTypes.SET_CONFIRM_CHANGE_STORE, GLOBAL_SETTING.confirm_change_store)
}

Vue.mixin({
    methods: {
        trans(key, replace) {
            return trans(key, replace)
        }
    }
})


//--------------- Modules ------------------------//
const makroApp = new Vue({
    store,
    el: '#makro-app',
    computed: {
        layoutClass: function () {
            return {
                'grid-view': this.$store.state.productDisplayer.type == 'grid',
                'list-view': this.$store.state.productDisplayer.type == 'list'
            }
        },
        isCartEmpty: function () {
            return this.$store.getters.cartCount < 1 ? true : false;
        },
        isMinimumCheckoutPassed: function () {
            return this.$store.getters.grandTotal >= parseInt(CART_DATA.minimumCheckout);
        },
        isMaximumCheckout: function () {
            return this.$store.getters.cartCount <= parseInt(CART_DATA.maximumCheckout);
        }
    },
    data: {
        member_data: typeof MEMBER_DATA == 'object' ? MEMBER_DATA : null,
        cart_data: typeof CART_DATA == 'object' ? CART_DATA : null
    }
})

//Login successfully popup
if (typeof GLOBAL_SETTING == 'object') {
    if (GLOBAL_SETTING.login_success == 1) {
        let addressType = store.state.storeModule.selected_address.type
        let postcode = store.state.storeModule.selected_address.postcode
        let selected_address = store.state.storeModule.selected_address
        // console.log(store.getters)
        // return false
        if(addressType == 'address') {
            axios.get(`${store.getters.locale_url}/address/delivery-by-postcode?postcode=${postcode}`)
            .then(response => {
                let data = response.data
                if(data.length > 0) {
                    let address = data.find(item => item.sub_districts.id == selected_address.sub_districts.id && item.postcode == selected_address.postcode)
                   if(address) {
                       if (selected_address.store_price != address.store_price) {
                            selected_address.store_price = address.store_price
                            storeManager.set('SELECTED_ADDRESS', selected_address)
                            let payload = {
                                store_price_id: selected_address.store_price
                            }
                            setCurentStore(payload)
                        }
                    }
                }
                
            })
            .catch(error => {
                console.log(error)
            })
        } else {
            axios.get(store.getters.locale_url + '/stores/pickup-store')
                .then(function (response) {
                    //Success
                    let data = response.data
                    if (data.length > 0) {
                        let address = data.find(item => item.id == selected_address.store_id)
                        if (address) {
                            if (selected_address.store_price != address.store_price) {
                                selected_address.store_price = address.store_price
                                storeManager.set('SELECTED_ADDRESS', selected_address)
                                let payload = {
                                    store_price_id: selected_address.store_price
                                }
                                setCurentStore(payload)
                            }
                        }
                    }
                })
                .catch(function () {
                    //Error
                });
        }
        // popup.open({
        //     type: 'success',
        //     message: store.getters.lang.login_success,
        //     confirmText: store.getters.lang.ok,
        //     onClose: function () {
        //         window.location.reload();
        //     }
        // })
    }
}

function setCurentStore(payload) {
    axios.post(store.getters.locale_url + '/stores/set-current-store', payload)
        .then(response => {
            location.reload()
        })
        .catch(error => {

        });
}


// Init Popover
$('[data-toggle="popover"]').popover({
    html: true,
    container: 'body'
});

// Init Select2
$('select').select2({
    theme: 'bootstrap',
    width: 'auto',
    minimumResultsForSearch: -1,
    allowClear: true,
});


if (GLOBAL_SETTING.show_popup_error === 1) {
    let message = ''
    if (typeof GLOBAL_SETTING.errors === 'string') {
        message = GLOBAL_SETTING.errors
    } else if (typeof GLOBAL_SETTING.errors === 'object' || typeof GLOBAL_SETTING.errors === 'array') {
        let tempMessages = []
        for (let i in GLOBAL_SETTING.errors) {
            tempMessages.push(GLOBAL_SETTING.errors[i])
        }

        message = tempMessages.join('<br />')
    }

    popup.open({
        type: 'error',
        message: message,
        title: store.state.appModule.lang.could_not_add_product_to_cart
    })
}

function showPopupConnectionError(type = 'error', confirmText = GLOBAL_SETTING.lang.btn_confirm, reloadPage = 0) {
    popup.open({
        type: type,
        message: GLOBAL_SETTING.lang.there_was_a_connection_problem_please_retry_again,
        confirmText: confirmText,
        preventClose: true,
        confirm: function (done) {
            if (reloadPage === 1) {
                window.location.reload()
            } else {
                popup.close()
            }
        }
    })
}


//
// if (GLOBAL_SETTING.route_name === 'home.index'
//     || GLOBAL_SETTING.route_name === 'members.register.facebook'
// ) {
//     history.pushState('', document.title, window.location.pathname)
// }


window.reCaptchaValid = false;
window.reCaptchaCallback = function (response) {
    reCaptchaValid = response;
    let passed = reCaptchaValid === false ? false : true;
    store.commit(mutationTypes.SET_RECAPTCHA_CHECKED, passed)
}

function calculateBannerDimension() {
    if (window.outerWidth <= 480) {
        let boxCenterHeight = $('.box-banner-center').outerHeight()
        let boxLeftHeight = $('.box-banner-left').outerHeight()
        let boxLeftWidth = $('.box-banner-left').outerWidth()
        let boxRightHeight = $('.box-banner-right').outerHeight()
        let boxRightWidth = $('.box-banner-right').outerWidth()

        let newLeftWidth = (boxCenterHeight / boxLeftHeight) * boxLeftWidth
        let newRightWidth = (boxCenterHeight / boxRightHeight) * boxRightWidth
        $('.box-banner-left').css({'max-width': parseInt(newLeftWidth) + 'px'})
        $('.box-banner-right').css({'max-width': parseInt(newRightWidth) + 'px'})

        const allWidth = $('.box-banner-left').outerWidth() + $('.box-banner-center').outerWidth() + $('.box-banner-right').outerWidth()
        $('.box-categories-container .box-menu-catagories').css({'width': allWidth + 'px'})
    }

}

function manageBannerButtons(obj) {
    if (obj) {
        const containerWidth = document.querySelector('.box-categories-container .box-menu-catagories').clientWidth
        const scrollOffsetWidth = obj.scrollWidth - obj.offsetWidth
        const scrollLeft = obj.scrollLeft

        let showPrev = false
        let showNext = false
        if (scrollLeft <= 50) {
            showNext = true
        }

        if (scrollLeft >= scrollOffsetWidth - 50) {
            showPrev = true
        }

        if (!showPrev && !showNext) {
            showPrev = true
            showNext = true
        }

        if (showPrev) {
            $(obj).parent().find('.prev').removeClass('hide')
        } else {
            $(obj).parent().find('.prev').addClass('hide')
        }

        if (showNext) {
            $(obj).parent().find('.next').removeClass('hide')
        } else {
            $(obj).parent().find('.next').addClass('hide')
        }
    }
}

$(function () {
    if (document.querySelector('.box-categories-scroll')) {
        calculateBannerDimension()

        $('.box-categories-scroll').each(function() {
            manageBannerButtons(this)
        })

        $('.box-categories-scroll').on('scroll', function (e) {
            manageBannerButtons(e.target)
        })
    }
})

