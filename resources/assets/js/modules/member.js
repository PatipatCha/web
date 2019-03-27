import store from '../store'
import * as mutationTypes from '../store/mutation-types'
import ForgotPassword from '../components/member/forgot_password/ForgotPassword'
import MakroCardIdField from '../components/member/register/field/MakroCardId'
import ShippingAddress from '../components/cart/checkout/shipping_address/Container'

Vue.component('forgot-password', ForgotPassword)
Vue.component('makro-card-id-field', MakroCardIdField)
Vue.component('shipping-address', ShippingAddress)

if (typeof SHIPPING_DATA == 'object') {
  store.commit(mutationTypes.SET_SHIPPING_ADDRESS, SHIPPING_DATA);
  store.commit(mutationTypes.SET_SHIPPING_DISPLAY, SHIPPING_DATA[0])
}

store.commit(mutationTypes.SET_SHIPPING_SELECT, "0")

$(function () {
  if ($('#tax-form').length) {
    $('#tax-form').validate({
      rules: {
        tax_branch: {
          required: true,
          digits: true,
          maxlength: 5
        },
        tax_tax_id: {
          required: true,
          digits: true,
          minlength: 13,
          maxlength: 13
        },
        tax_mobile_phone: {
          required: true,
          digits: true,
          minlength: 10,
          maxlength: 10
        },
        tax_email: {
          maxlength: 150,
          email: true
        },
        tax_address: {
          required: true,
          maxlength: 100
        },
        bill_first_name: {
          maxlength: 64
        },
        bill_last_name: {
          maxlength: 64
        },
        bill_mobile_phone: {
          digits: true,
          minlength: 10,
          maxlength: 10
        },
        bill_address: {
          maxlength: 100
        },
        bill_contact_email: {
          email: true
        }
      }
    });
  }

  if ($('#profile-form').length) {

    $('#editProfileModal').on('show.bs.modal', function () {
      $('input,select,textarea', $('#profile-form')).removeClass('error')
      $('label.error', $('#profile-form')).hide()
    })

    $('.submit-profile-btn').click(() => {
      $('#profile-form').trigger('submit')
    })

    $('#profile-form').on('submit', (event) => {
      console.log('_IS_FORM_SUBMITTING', true)
      _IS_FORM_SUBMITTING = true
    })

    $('input,select,textarea', $('#profile-form')).on('keyup change', () => {
      console.log('_IS_FORM_SUBMITTING', false)
      _IS_FORM_SUBMITTING = false
    })

    _VALIDATOR_FORM = $('#profile-form')
    _VALIDATOR = $('#profile-form').validate({
      rules: {
        shop_name: {
          'no-special-char': true
        },
        phone: {
          required: true,
          digits: true,
          minlength: 10,
          maxlength: 10,
        },
        first_name: {
          required: true,
          maxlength: 64,
          'no-special-char': true
        },
        last_name: {
          required: true,
          maxlength: 64,
          'no-special-char': true
        },
        address: {
          required: true,
          // maxlength: 100,
          'no-special-char': true
        },
        email: {
          maxlength: 150
        }
      }
    });

  }

  $('#profile-form').submit(function (even) {

    if ($('.form-control-MakroCardID').length > 0 && $('.form-control-MakroCardID').attr('checkcard') != 'true') {
      $('#error-makro-id').show().fadeOut(3000)
      event.preventDefault()
    }

  })

  $('.sort_order').change(function () {

    var url = store.getters.locale_url + '/members/orders?sort_by=' + $(this).val()
    location.href = url

  })

  if ($('#reset-password').length) {
    $('#reset-password').validate({
      rules: {
        password: {
          required: true,
          'valid-password': true,
          minlength: 8
        },
        confirm_password: {
          equalTo: "#reset-password1"
        },
        reset_code: {
          required: true
        }
      }
    })
  }

  if ($('#contact-us-form').length) {
    $('#contact-us-form').validate({
      rules: {
        makro_card_id: {
          number: true
        },
        phone: {
          required: true,
          digits: true,
          minlength: 10,
          maxlength: 10
        },
        email: {
          email: true,
          maxlength: 150,
          required: true
        },

        full_name: {
          required: true,
          maxlength: 128,
        },

        detail: {
          required: true
        }
      },
      invalidHandler: function (result, validator) {
        console.log('error')
      }

    });
  }
});

jQuery.validator.addMethod("alphanumeric", function (value, element) {
  return this.optional(element) || /^\w+$/i.test(value);
}, "Letters, numbers, and underscores only please");