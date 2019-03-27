
import * as types from '../mutation-types'

//State
const state = {
    url: '',
    locale_url: '',
    product_list_url: '',
    logged_in: 0,
    login_callback: null,
    provinces: [],
    cities: [],
    sub_districts: [],
    pickup_times: '',
    showStorePickup: false,
    showStorePickupCallback: null,
    reCaptchaSiteKey: '',
    confirm_change_store: 0,
    lang: null,
    locale: 'th',
    popup: {
        show: false,
        title: '',
        message: '',
        type: 'info',
        center: true,
        confirm: null,
        cancel: null,
        size: 'normal',
        confirmText: '',
        cancelText: '',
        onClose: null,
        preventClose: false,
        showLoading: true,
        btnId: '',
        errorText: ''
    },
    show_store_selector: 0,
    is_accept_delivery: 0,
    recaptchaChecked: false
}

//Mutations
const mutations = {
    [types.SET_URL] (state, url) {
        state.url = url;
    },

    [types.SET_LOCALE_URL] (state, url) {
        state.locale_url = url;
    },

    [types.SET_PRODUCT_LIST_URL] (state, url) {
        state.product_list_url = url;
    },

    [types.SET_LOGGED_IN ]  (state, status) {
        state.logged_in = status;
    },

    [types.SET_LOGIN_CALLBACK ]  (state, callback) {
        state.login_callback = callback;
    },

    [types.SET_PROVINCE] (state, items) {
        state.provinces = items;
    },

    [types.SET_CITY] (state, items) {
        state.cities = items;
    },

    [types.SET_SUB_DISTRICT] (state, items) {
        state.sub_districts = items;
    },

    [types.SET_PICKUP_TIME] (state, time) {
        var times = time.split(',');
        state.pickup_times = times;
    },

    [types.CHANGE_SHOW_PICKUP_STORE] (state, status) {
        state.showStorePickup = status
    },

    [types.SET_SHOW_PICKUP_STORE_CALLBACK] (state, callback) {
        state.showStorePickupCallback = callback
    },

    [types.SET_RECAPTCHA_SITE_KEY] (state, sitekey) {
        state.reCaptchaSiteKey = sitekey
    },
    [types.SET_CONFIRM_CHANGE_STORE] (state, status) {
        state.confirm_change_store = status
    },
    [types.SET_LANG] (state, lang) {
        state.lang = lang
    },
    [types.SET_LOCALE] (state, locale) {
        state.locale = locale
    },
    [types.SET_POPUP] (state, options) {
        for (var i in options) {
            state.popup[i] = options[i]
        }
    },
    [types.SET_SHOW_STORE_SELECTOR] (state, value) {
        state.show_store_selector = value
    },
    [types.SET_IS_ACCEPT_DELIVERY] (state, value) {
        state.is_accept_delivery = value
    },
    [types.SET_RECAPTCHA_CHECKED] (state, value) {
        state.recaptchaChecked = value
    },
    [types.SET_POPUP_ERROR_TEXT] (state, text) {
        state.popup.errorText = text
    }
}

//Getters
const getters = {
    url: state => state.url,
    locale_url: state => state.locale_url,
    locale: state => state.locale,
    product_list_url: state => state.product_list_url,
    lang: state => state.lang,
    logged_in: state => state.logged_in,
    productFilterLang: state => {
        const data = [
            {'filters[lang]': state.locale}
        ];

        return data;
    },
    showStoreSelector: state => {
        return state.show_store_selector
    },
    popupOption: state => {
        return state.popup
    },
    recaptchaChecked: state => {
        return state.recaptchaChecked
    }
}


export default {
    state,
    mutations,
    getters
}