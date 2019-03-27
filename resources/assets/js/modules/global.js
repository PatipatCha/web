import Loading from '../components/loading/Loading'
import store from '../store'
import AddToCartButton from '../components/cart/AddToCartButton'
import AddToWishListButton from '../components/wishlist/AddToWishListButton'
import 'jquery-validation'
import ResidenceAddress from '../components/cart/checkout/address/Residence'
import DatePicker from '../components/input_fields/DatePicker'
import DefaultPopup from '../components/popup/Default'
import DeliveryAcceptancePopup from '../components/popup/DeliveryAcceptance'
import {
  SET_LOGIN_CALLBACK,
  SET_ADDRESSES,
  SET_SELECTED_ADDRESS
} from '../store/mutation-types'
import StoreAddress from '../libraries/store/store_address'

Vue.component('loading', Loading)
Vue.component('residence-address', ResidenceAddress)
Vue.component('date-picker', DatePicker)
Vue.component('default-popup', DefaultPopup)
Vue.component('delivery-acceptance-popup', DeliveryAcceptancePopup)

window.onLogout = (redirect) => {
  const storeAddress = new StoreAddress
  storeAddress.set([])
  store.commit(SET_ADDRESSES, [])

  storeAddress.setSelected(null)

  setTimeout(() => {
    window.location = redirect
  }, 0)
}


$(function () {
  if ($('.owl-carousel').length) {

    function reCreateAddButtons() {
      //Add to cart button
      var els = $('.owl-carousel .add-to-cart-wrapper');
      els.each(function () {
        var productId = $(this).attr('data-id');
        var product = JSON.stringify(CAROUSEL_PRODUCTS[productId])
        var addToCartWrapper = {
          'template': '<div data-id="' + productId + '" class="add-to-cart-wrapper"><div class="box-b-cart pull-left"><add-to-cart-button product=\'' + product + '\' v-bind:button_type="2"></add-to-cart-button></div></div>'
        };

        new Vue({
          store,
          el: this,
          components: {
            'add-to-cart-button': AddToCartButton
          },
          render: h => h(addToCartWrapper)
        });
      });


      //Add to wish list button
      var wish_list_els = $('.owl-carousel .add-to-wish-list-wrapper');
      wish_list_els.each(function () {
        var productId = $(this).attr('data-id');
        var product = JSON.stringify(CAROUSEL_PRODUCTS[productId])
        var addToWishListWrapper = {
          'template': '<div data-id="' + productId + '" class="add-to-wish-list-wrapper"><div class="box-b-wishlist pull-right"><add-to-wish-list-button product=\'' + product + '\'></add-to-wish-list-button></div></div>'
        };
        new Vue({
          store,
          el: this,
          components: {
            'add-to-wish-list-button': AddToWishListButton
          },
          render: h => h(addToWishListWrapper)
        });
      });
    }

    reCreateAddButtons();
    $('.owl-carousel').on('changed.owl.carousel', function () {
      setTimeout(() => {
        reCreateAddButtons();
      }, 500)

    })

    $('.owl-carousel').on('translated.owl.carousel', function () {
      setTimeout(() => {
        reCreateAddButtons();
      }, 500)
    })
  }


  //Need login link
  $(document).on('click', '.need-login', function (e) {
    if (!store.state.appModule.logged_in) {
      e.preventDefault()

      var url = $(this).attr('href')
      $('#login-modal').modal('show');
      store.commit(SET_LOGIN_CALLBACK, function () {
        window.location = url
      })
    }
  })

  function headerFix() {
    var windowPos = $(window).scrollTop();
    // var mainContentOffset = $(window).offset().top;
    var headerHeight = $(window).height();
    var mainContentOffset = $(window).height();
    var distance = (mainContentOffset - windowPos);
    if (distance < headerHeight) {
      $('.header-main').addClass('affix');
    } else {
      $('.header-main').removeClass('affix');
    }
  }

  $(window).on('scroll', function () {
    headerFix();
  });

  $('#menu-mobile').on('shown.bs.offcanvas', function () {
    // $('.nav .dropdown').('active open');
  })
});

/*
 * Jquery validate custom rules
 */
window._VALIDATOR = null
window._VALIDATOR_FORM = null
window._IS_FORM_SUBMITTING = false

let dnsPassed = true
let checkedDnsEmails = []
let errorDnsEmails = []


$(document).on('blur', '.email-field', function (e) {
  console.log($(this).val())
  $(this).val($(this).val().toLowerCase())
})
const onEmailValidate = _.debounce((email, element) => {
  // $('.error', $(element).parent()).text(store.getters.lang.validating_email).show()
  axios.post(store.getters.url + '/validate-email', {
    email
  })
    .then((response) => {
      if (_.get(response, 'data.status') == 'success') {
        dnsPassed = true
        $('label.error', $(element).parent()).hide()
        $('input', $(element).parent()).removeClass('error')
        checkedDnsEmails.push(email)

        if (_IS_FORM_SUBMITTING) {
          if (_VALIDATOR && _VALIDATOR_FORM) {
            _VALIDATOR.element(element)
            const errorList = _.get(_VALIDATOR, 'errorList')
            if (!errorList.length) {
              console.log('submit')
              _VALIDATOR_FORM.trigger('submit')
            }
          }
        }
      } else {
        errorDnsEmails.push(email)
        dnsPassed = false
        $('label.error', $(element).parent()).text(store.getters.lang.please_enter_valid_email_format)
      }
    })
    .catch((err) => {
      dnsPassed = false
      //TODO: handle error later
    })
}, 800)
jQuery.validator.addMethod("email", function (value, element) {
  dnsPassed = false
  var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  let passed =  this.optional(element) || re.test(value);

  if (passed && (!dnsPassed && value.length > 0)) {
    console.log('checkedDnsEmails')
    console.log(checkedDnsEmails)
    if (checkedDnsEmails.indexOf(value) > -1) {
      return true
    }

    if (errorDnsEmails.indexOf(value) > -1) {
      return false
    }

    setTimeout(() => {
      $('label.error', $(element).parent()).text(store.getters.lang.validating_email).show()
    }, 0)

    onEmailValidate(value, element)
    return false
  }

  return passed;
}, store.getters.lang.validate_email);

jQuery.validator.addMethod("valid-password", function (value, element) {
  var re = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/;
  return this.optional(element) || re.test(value);
}, store.getters.lang.password_requirement);

jQuery.validator.addMethod("requirednozero", function (value, element) {
  let val = parseInt(value)
  if (isNaN(val) || val < 1) {
    return false
  }

  return true
}, store.getters.lang.validate_required);

let max = 45
jQuery.validator.addMethod("address_line1", function (value, element) {

  if (value.length > max) {
    return false
  }

  return true
}, store.getters.lang.please_enter_no_more_than + ' ' + max + ' ' + store.getters.lang.characters);


jQuery.validator.addMethod("no-special-char", function (value, element) {
  if (value && value.length && value.match(/\{|\}|<|>/gi)) {
    return false
  }

  return true
}, store.getters.lang.special_chars_not_allowed);

jQuery.validator.addMethod("no-alpha-char", function (value, element) {
  if (value && value.length && value.match(/\{|\}|<|>/gi)) {
    return false
  }

  return true
}, store.getters.lang.special_chars_not_allowed);

jQuery.validator.setDefaults({
  showErrors: function (errorMap, errorList) {

    if (errorList && errorList.length) {
      // $(errorList[0].element).parent())
      //$('.special-char-error', $(errorList[0].element).parent()).remove()
    }
    this.defaultShowErrors();
  },
  onfocusout: false
})


$(() => {
  $(document).on('keyup', 'input[type="text"], input[type="email"], input[type="number"], input[type="tel"], input[class="post-code-input"]', (e) => {
    const x = event.which || event.keyCode;
    if (x == 8) {
      $('.special-char-error', $(e.target).parent()).remove()
    }
  })

  $(document).on('keypress', 'input[type="text"], input[type="email"], input[type="number"], input[type="tel"], .post-code-input', (e) => {
    if (!$(e.target).hasClass('not-prevent-special-chars')) {
      //$('.special-char-error', $(e.target).parent()).remove()
      $('label.error', $(e.target).parent()).remove()
      $('div.error', $(e.target).parent()).remove()
      const x = event.which || event.keyCode;
      //60 = <,  62 = >, 123 = {, 125 = }
      const preventCodes = [60, 62, 123, 125]
      if (preventCodes.indexOf(x) > -1) {
        $(e.target).after('<div class="error special-char-error">' + store.getters.lang.special_chars_not_allowed + '</div>')
        e.preventDefault()
        return false
      }
    }
  })

  $(document).on('paste', 'input[name="username"]', (e) => {
    let key = e.originalEvent.clipboardData.getData('text')
    const specialChar = /([\{\}\<\>])/;
    if (specialChar.test(key)) {
      if (!$(e.target).hasClass('not-prevent-special-chars')) {
        $('.special-char-error', $(e.target).parent()).remove()
        $('label.error', $(e.target).parent()).remove()
        $('div.error', $(e.target).parent()).remove()
        $(e.target).after('<div class="error special-char-error">' + store.getters.lang.special_chars_not_allowed + '</div>')
        e.preventDefault()
      }
    }
  })

  $(document).on('keypress', 'input[name="username"]', (e) => {
    let key = event.key
    const filter = /^([ก-๙])+$/
    const preventCodes = [60, 62, 123, 125]
    const x = event.which || event.keyCode;
    if (filter.test(key) && preventCodes.indexOf(x) == -1) {
    // if(x > 3500) {
      if (!$(e.target).hasClass('not-prevent-special-chars')) {
        $('.special-char-error', $(e.target).parent()).remove()
        $('label.error', $(e.target).parent()).remove()
        $('div.error', $(e.target).parent()).remove()
        $(e.target).after('<div class="error special-char-error">' + store.getters.lang.not_allow_th_char + '</div>')
        e.preventDefault()
      }
    }
  })

  $(document).on('paste', 'input[name="username"]', (e) => {
    let key = e.originalEvent.clipboardData.getData('text')
    let filter = /([ก-๙])/; //^([a-zA-Z0-9_\.\-\@\{\}\<\>])+$/
    let special = /([\{\}\<\>])/;
    if (filter.test(key) && !special.test(key)) {
      if (!$(e.target).hasClass('not-prevent-special-chars')) {
        $('.special-char-error', $(e.target).parent()).remove()
        $('label.error', $(e.target).parent()).remove()
        $('div.error', $(e.target).parent()).remove()
        $(e.target).after('<div class="error special-char-error">' + store.getters.lang.not_allow_th_char + '</div>')
        e.preventDefault()
      }
    }
  })

  // $(document).on('blur', 'input[type="text"], input[type="email"], input[type="number"], input[type="tel"]', (e) => {
  //     if (!$(e.target).hasClass('not-prevent-special-chars')) {
  //         //$('.special-char-error', $(e.target).parent()).remove()
  //         $('label.error', $(e.target).parent()).remove()
  //         $('div.error', $(e.target).parent()).remove()
  //         if (e.target.value.match(/\{|\}|<|>/gi)) {
  //             $(e.target).after('<div class="error special-char-error">' + store.getters.lang.special_chars_not_allowed + '</div>')
  //             e.preventDefault()
  //         }
  //     }
  // })
})


// jQuery.validator.methods.maxlength = function (value, element, param) {
//     var m = encodeURIComponent(value).match(/%[89ABab]/g);
//     var length = value.length + (m ? m.length : 0);
//     return length <= param;
// }
//
// jQuery.validator.methods.minlength = function (value, element, param) {
//     var m = encodeURIComponent(value).match(/%[89ABab]/g);
//     var length = value.length + (m ? m.length : 0);
//     return length >= param;
// }

jQuery.extend(jQuery.validator.messages, {
  required: store.getters.lang.validate_required,
  remote: store.getters.lang.please_fix_this_field,
  email: store.getters.lang.validate_email,
  url: store.getters.lang.please_enter_valid_url,
  date: store.getters.lang.please_enter_valid_date,
  dateISO: store.getters.lang.please_enter_valid_date_iso,
  number: store.getters.lang.validate_number,
  digits: store.getters.lang.please_enter_only_digits,
  creditcard: store.getters.lang.please_enter_valid_credit_card_number,
  equalTo: store.getters.lang.validate_equalto,
  accept: store.getters.lang.please_enter_value_with_valid_extension,
  maxlength: jQuery.validator.format(store.getters.lang.please_enter_no_more_than_characters),
  minlength: jQuery.validator.format(store.getters.lang.validate_minlength),
  rangelength: jQuery.validator.format(store.getters.lang.please_enter_value_between_and_characters_long),
  range: jQuery.validator.format(store.getters.lang.please_enter_value_between),
  max: jQuery.validator.format(store.getters.lang.please_enter_a_value_less_than_or_equal_to),
  min: jQuery.validator.format(store.getters.lang.please_enter_a_value_greater_than_or_equal_to)
});

$.validator.setDefaults({
  ignore: ':hidden:not(.do-not-ignore)'
});
