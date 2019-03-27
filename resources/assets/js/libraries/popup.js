import store from '../store'
import {SET_POPUP, SET_POPUP_ERROR_TEXT} from '../store/mutation-types'

export default {
  //type = 'success', 'info', 'error', 'warning', 'confirm'  ////  เฉพาะ type confirm ที่ขึ้นสองปุ่ม (cancel, confirm) นอกนั้นขึ้น ปุ่มเดียว(confirm)
  open: function (options, message, type = 'info', center = true, confirmCallback = null, cancelCallback = null, wide = false, id = '') {
    //Backward compatible  (ไม่อยากให้ใช้โหมดนี้)
    if (arguments.length > 1 && typeof options == 'string') {
      store.state.appModule.popup.show = true
      store.state.appModule.popup.title = options
      store.state.appModule.popup.message = message
      store.state.appModule.popup.type = type
      store.state.appModule.popup.center = center
      store.state.appModule.popup.confirm = confirmCallback
      store.state.appModule.popup.cancel = cancelCallback
      store.state.appModule.popup.confirmText = store.getters.lang.ok
      store.state.appModule.popup.cancelText = store.getters.lang.cancel
      store.state.appModule.popup.btnId = id
    } else {
      //Current recommended!!!
      var defaultOptions = {
        title: '',
        message: '',
        type: 'info', //type = 'success', 'info', 'error', 'warning', 'confirm'  ////  เฉพาะ type confirm ที่ขึ้นสองปุ่ม (cancel, confirm) นอกนั้นขึ้น ปุ่มเดียว(confirm)
        confirm: null,  // Click Confirm button callback
        cancel: null, // Click Cancel button callback
        size: 'normal', // 'normal', 'wide', 'full'
        center: true,
        confirmText: store.getters.lang.ok,
        cancelText: store.getters.lang.cancel,
        onClose: null,  // ตอน Modal ปิดตัวเองไม่ว่าจะปิดด้วยวิธีไหนก็ตาม
        preventClose: false,
        showLoading: false,
        btnId: ''
      }

      options = $.extend(defaultOptions, options)
      options.show = true

      console.log(options)
      store.commit(SET_POPUP, options)
    }
  },

  close: function () {
    var options = {
      show: false,
      showLoading: false
    };
    store.commit(SET_POPUP, options)
  },

  setErrorText(text) {
    store.commit(SET_POPUP_ERROR_TEXT, text)
  }
}