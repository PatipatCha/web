<template>
    <div>
        <modal v-bind:show="show" title="Edit buyer information" additional-custom-classes="address-modal-box"
               v-on:on-close-modal="onCloseModal" v-on:on-before-open-modal="onOpenModal">
            <div slot="header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title"><b>{{ title }}</b></h5>
            </div>
            <div>
                <form method="post" action="#" ref="address_modal_form" v-bind:id="formId">
                    <cart-checkout-address
                            v-bind:email-required="emailRequired"
                            v-bind:input-name="inputName"
                            v-bind:init-data="initData"
                            v-bind:member-profile="memberProfile"
                            v-bind:ignore-validate-class="ignoreValidateClass"
                            :use-delivery-residence-address="useDeliveryResidenceAddress"
                            :shop-name-id="shopNameId"
                            :first-name-id="firstNameId"
                            :last-name-id="lastNameId"
                            :telephone-number-id="telephoneNumberId"
                            :email-id="emailId"
                            :address-name-id="addressId"
                            :province-id="provinceId"
                            :district-id="districtId"
                            :sub-district-id="subDistrictId"
                            :btn-save-id="btnSaveId"
                            :btn-cancel-id="btnCancelId"
                            :label-first-name="labelFirstName"
                            :label-last-name="labelLastName"
                            :label-mobile-number="labelMobileNumber"
                            :label-email="labelEmail"
                            :label-address="labelAddress"
                            :label-province="labelProvince"
                            :label-district="labelDistrict"
                            :label-sub-district="labelSubDistrict"
                            :label-postcode="labelPostcode"
                            :postcode-id="postcodeId"
                    >
                    </cart-checkout-address>
                </form>
            </div>
            <div slot="footer">
                <button type="button" class="btn btn-default pull-left" :id="getBtnCancelId" data-dismiss="modal">
                    <i class="far fa-times"></i>
                    {{ trans('cancel')}}
                </button>
                <button type="button" class="btn btn-primary pull-right" :id="getBtnSaveId" v-on:click="submit">
                    <i class="far fa-check"></i> {{ lang.btn_confirm }}
                    <loading v-bind:show="submitting"></loading>
                </button>
            </div>
        </modal>
    </div>
</template>

<script>
  import CartCheckoutAddress from './Address'
  import Modal from '../../../bootstrap/Modal'
  import Loading from '../../../loading/Loading'

  export default {
    name: 'address-modal',
    components: {
      'cart-checkout-address': CartCheckoutAddress,
      'modal': Modal,
      'loading': Loading,
    },
    props: {
      title: {
        type: String,
        default: 'Address'
      },
      formId: {
        type: String,
        default: 'address_form'
      },
      initData: {
        type: Object
      },
      inputName: {
        type: String,
        default: ''
      },
      show: {
        type: Boolean,
        default: false
      },
      submitting: {
        type: Boolean,
        default: false
      },
      detectRandom: {
        type: Boolean,
        default: true
      },
      random: {
        type: Number,
        default: 0
      },
      emailRequired: {
        type: Boolean,
        default: true
      },
      memberProfile: {
        type: Object
      },
      ignoreValidateClass: {
        type: String
      },
      useDeliveryResidenceAddress: {
        type: Boolean,
        default: false
      },
      shopNameId: {
        type: String,
        default: ''
      },
      firstNameId: {
        type: String,
        default: ''
      },
      lastNameId: {
        type: String,
        default: ''
      },
      telephoneNumberId: {
        type: String,
        default: ''
      },
      emailId: {
        type: String,
        default: ''
      },
      addressId: {
        type: String,
        default: ''
      },
      provinceId: {
        type: String,
        default: ''
      },
      districtId: {
        type: String,
        default: ''
      },
      subDistrictId: {
        type: String,
        default: ''
      },
      btnSaveId: {
        type: String,
        default: ''
      },
      btnCancelId: {
        type: String,
        default: ''
      },
      labelFirstName: {
        type: String,
        default: ''
      },
      labelLastName: {
        type: String,
        default: ''
      },
      labelMobileNumber: {
        type: String,
        default: ''
      },
      labelEmail: {
        type: String,
        default: ''
      },
      labelAddress: {
        type: String,
        default: ''
      },
      labelProvince: {
        type: String,
        default: ''
      },
      labelDistrict: {
        type: String,
        default: ''
      },
      labelSubDistrict: {
        type: String,
        default: ''
      },
      labelPostcode: {
        type: String,
        default: ''
      },
      postcodeId: {
        type: String,
        default: ''
      }
    },
    data: function () {
      return {
        showModal: this.show
      }
    },
    mounted: function () {
      var form = $(this.$refs.address_modal_form);

      form.on('submit', () => {
        console.log('_IS_FORM_SUBMITTING', true)
        _IS_FORM_SUBMITTING = true
      })

      $('input,select,textarea', form).on('keyup change', () => {
        console.log('_IS_FORM_SUBMITTING', false)
        _IS_FORM_SUBMITTING = false
      })

    },
    methods: {
      submit: function () {
        $(this.$refs.address_modal_form).trigger('submit');
      },
      formatData: function (datas) {
        var newData = {};
        for (var i = 0; i < datas.length; ++i) {
          newData[datas[i].name] = datas[i].value;
        }

        return newData;
      },
      onCloseModal: function () {
        this.$emit('on-add-address-modal-close')
        var form = $(this.$refs.address_modal_form);

        $('label.error', form).hide();
        $('div.error', form).hide();
      },
      onOpenModal() {
        $('label.error', $('#' + this.formId)).hide()
        $('div.error', $('#' + this.formId)).hide()

        this.initValidate()
      },
      initValidate() {
        var $this = this;
        var form = $(this.$refs.address_modal_form);

        $('input,select,textarea', form).removeClass('error')

        form.validate().destroy()

        _VALIDATOR_FORM = form
        _VALIDATOR = form.validate({
          submitHandler: function (e) {
            $this.$emit('on-success-submit', $this.formatData(form.serializeArray()));
            return false;
          },
          invalidHandler: function (result) {
            $this.$emit('on-error-submit');
            return false;
          }
        });
      }
    },
    computed: {
      lang: function () {
        return this.$store.getters.lang;
      },
      getBtnSaveId() {
        if (_.isEmpty(this.btnSaveId)) {
          return false
        }

        return this.btnSaveId
      },
      getBtnCancelId() {
        if (_.isEmpty(this.btnCancelId)) {
          return false
        }

        return this.btnCancelId
      }
    }
  }
</script>