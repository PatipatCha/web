<template>
    <modal
            :show="showModal"
            content-size="full"
            :prevent-close="true"
            class="modal-shopping-option "
            @on-open-modal="onOpenModal"
            @on-close-modal="onCloseModal"
            :show-body="showModalBody"
            :show-footer="showFooter"
            @on-before-open-modal="onBeforeOpenModal"
    >
        <div slot="header" class="text-center">
            <h4 class="modal-title pull-left">{{ trans('shipping_or_pickup_on_your_own') }}</h4>
            <div class="box-select-language" v-if="showLanguageSwitcher">
                <div role="group" class="btn-group btn-group-sm">
                    <a href="javascript:;" @click="changeLanguage(getLangUrl('th'))" class="btn btn-default navbar-btn"
                       :class="locale === 'th'? 'active': ''">ไทย</a>
                    <a href="javascript:;" @click="changeLanguage(getLangUrl('en'))" class="btn btn-default navbar-btn"
                       :class="locale === 'en'? 'active': ''">EN</a></div>
            </div>
        </div>
        <div v-if="store_error_message" class="row">
          <div class="text-left" style="color:red; margin-bottom:15px; margin-left:15px;">{{ store_error_message }}</div>
        </div>
        <div>
          
            <ul class="nav nav-tabs" role="tablist">
                <li role="shopping-option" :class="shipping_type == 'address'? 'active' : ''"
                    @click="selectType('address')" style="cursor: pointer">
                    <a aria-controls="delivery" role="tab" data-toggle="tab">
                        <div class="icon">
                            <i class="far fa-truck-container"></i>
                        </div>
                        <p class="title">{{ trans('delivery') }}</p>
                        <!--<p class="sub-title">{{ trans('add_convenience_fast_delivery') }}</p>-->
                    </a>
                </li>
                <li role="shopping-option" :class="shipping_type == 'store'? 'active' : ''"
                    @click="selectType('store')" :style="!disablePickupTap ?'cursor: pointer': ''">
                    <a aria-controls="pickup" role="tab" :class="disablePickupTap? 'disabled': ''"  :data-toggle="!disablePickupTap ?'tab': ''">
                        <div class="icon">
                            <i class="far fa-hand-holding-box"></i>
                        </div>
                        <p class="title">{{ pickupText }}</p>
                        <!--<p class="sub-title">{{ trans('choose_your_pickup_time') }}</p>-->
                    </a>
                </li>
            </ul>
        </div>
        <div slot="footer">
            <!-- Address form -->
            <!--<transition-->
            <!--name="custom-classes-transition"-->
            <!--enter-active-class="animated fadeIn"-->
            <!--&gt;-->
            <shipping-address-form
                    v-if="showAddressForm"
                    :on-cancel="onCancelShowAddressForm"
                    :on-success="onAddAddressSuccess"
                    :address="editAddress"
                    ref="shippingAddressForm"
            ></shipping-address-form>
            <!--</transition>-->

            <!-- Shipping option -->
            <!--<transition-->
            <!--name="custom-classes-transition"-->
            <!--enter-active-class="animated fadeIn"-->
            <!--&gt;-->
                <div
                        class="tab-content"
                        v-show="shipping_type && !showAddressForm && !isConfirmChangeAddress">
                    <delivery :address-id="addressId" :on-select-address="onSelectAddress"
                              :on-confirm="onConfirmAddress"
                              :active="shipping_type === 'address'"
                              :on-add-address="onAddAddress"
                              ref="delivery"
                              :on-edit="onEditAddress"
                              :error-address-type="error_address_type"
                              :submitting="submitting"
                              :submit-error="submitError"
                              :address-data="address"
                              :type="shipping_type"
                    ></delivery>
                    <pickup v-if="activeStore.length > 0" ref="pickup" :active="shipping_type === 'store'"
                            :on-select-address="onSelectAddress"
                            :on-submit-confirm-change-address="onConfirmAddress"
                            :submiting="submitting"
                            :type="shipping_type"
                    ></pickup>
                <div v-if="activeStore.length === 0" role="tabpanel" class="tab-pane" :class="shipping_type === 'store'? 'active': ''" id="pickup">
                  <div class="loading" v-if="submitting">
                      <i class="fas fa-sync fa-spin fa-2x"></i>
                  </div>
                  <div v-if="!submitting">
                    <div class="text-center pickup">
                      <p v-html="trans('pickup_coming_soon')"></p>
                    </div>
                    </div>
                  </div>
                </div>
            <!--</transition>-->


            <confirm-change-address
                    v-if="isConfirmChangeAddress"
                    :on-cancel="onCancelConfirmChangeAddress"
                    :on-submit="onSubmitConfirmChangeAddress"
                    :submitting="submitting"
                    :submit-error="submitError"
            ></confirm-change-address>
        </div>
    </modal>
</template>

<script>
  import Modal from '../../components/bootstrap/Modal2'
  import Delivery from './addressTap/Delivery'
  import Pickup from './addressTap/Pickup'
  import {mapActions, mapState, mapGetters} from 'vuex'
  import {
    SET_OPEN_POPUP_ADDRESS,
    SET_LOCALE,
    UPDATE_PREVENT_MOVE_ACTIVE_ADDRESS_TO_TOP,
    SET_ERROR_STORE_NOTAVILABLE_MESSAGE
  } from '../../store/mutation-types'
  import ShippingAddressForm from './ShippingAddressForm'
  import ConfirmChangeAddress from './ConfirmChangeAddress'
  import StoreAddress from '../../libraries/store/store_address'
  import { trans } from '../../libraries/trans'
  const storeAddress = new StoreAddress

  export default {
    name: "SelectAddressModal",
    components: {
      Modal,
      Delivery,
      Pickup,
      ShippingAddressForm,
      ConfirmChangeAddress
    },
    data() {
      return {
        shipping_type: '',
        type: '',
        store: null,
        store_id: null,
        showAddressForm: false,
        editAddress: null,
        error_address_type: false,
        submitError: '',
        submitting: false,
        addressToSubmit: null,
        isConfirmChangeAddress: false,
        address: null,
        deliveryTypes: {
            address: 'shipping',
            store: 'pickup'
        },
        activeStore: []
      }
    },
    computed: {
      ...mapState({
        selected_address: state => state.storeModule.selected_address,
        current_store_id: state => state.storeModule.current_store_id,
        store_error_message: state => state.storeModule.error_store_not_avilable_message
      }),
      ...mapGetters(['cartCount']),
      showModal: function () {
        // return false
        if (!this.$store.state.storeModule.selected_address) {
          return true
        }
        return this.$store.state.storeModule.open_popup_address
      },
      addressId: function () {
        return this.address ? _.get(this.address, 'id', '') : ''
      },
      showModalBody() {
        let show = false
        if (!this.showAddressForm && !this.isConfirmChangeAddress) {
          show = true
        }

        return show
      },
      showLanguageSwitcher() {
        let show = false
        if (!this.showAddressForm && !this.isConfirmChangeAddress) {
          show = true
        }

        if (this.shipping_type) {
          show = false
        }

        return show
      },
      locale: function () {
        return this.$store.getters.locale
      },
      showFooter: function () {
        return !_.isEmpty(this.shipping_type)
      },
      returnToCart() {
        return this.$store.state.storeModule.return_to_cart
      },
      disablePickupTap() {
        return this.activeStore.length === 0 && _.get(this.selected_address, 'type', '') !== 'store'
      },
      pickupText: function() {
        if(this.disablePickupTap) {
            return trans('pick_up_at_your_own_branch_soon')
        }
        return trans('pick_up_at_your_own_branch')
      },
    },
    methods: {
        ...mapActions([
            'getAddressFromApi',
            'getDeliveryStoreByPostCode',
            'updateCurrentStore',
            'setSelectedAddress',
            'updatePopupAddress',
            'getActivePickupStoresFromApi'
        ]),
        selectType(type) {
            if(type == 'store' && this.activeStore.length === 0) {
              if(!this.disablePickupTap) {
                this.shipping_type = type
              }
            }else{
              this.shipping_type = type
              this.address = null
              // this.$store.commit(SET_ERROR_STORE_NOTAVILABLE_MESSAGE, '')
              if (this.shipping_type == 'address') {
                  this.getAddressFromApi()
                  if(this.$refs.delivery) {
                    this.$refs.delivery.initialData()
                  }
              }
              if (this.shipping_type == 'store') {
                  if(this.$refs.pickup) {
                      this.$refs.pickup.initialData()
                  }
              }
            }
        },
        onSelectAddress(address) {
            this.address = address.address
            this.type = address.type
            this.store = address.store
            this.store_id = _.get(address, 'store_id', null)
            if(this.store_id) {
              this.address.store_id = this.store_id
            }
            this.error_address_type = false
        },
        onConfirmAddress() {
            this.submitError = ''
            this.submitting = false

            if (this.address) {
                //Has inventory store?
                const inventoryStoreId = _.get(this.address, 'main_inventory_store', null)
                if (inventoryStoreId) {
                    this.addressToSubmit = _.cloneDeep(this.address)
                    this.handleStoreChange()
                } else {
                    //(Not yet checking store) Check store by postcode
                    const postCode = _.get(this.address, 'postcode', null)
                    if (postCode) {
                        this.checkStore(postCode)
                    } else {
                        //No post code
                        this.submitError = this.trans('postcode_is_empty')
                    }
                }
            } else {
                this.error_address_type = true
            }
        },
        checkStore(postCode) {
          this.submitting = true
          this.getDeliveryStoreByPostCode(postCode)
          .then((response) => {
              this.submitting = false
            if (_.get(response, 'data.status') == 'error') {
              this.submitError = _.get(response, 'data.message')
            } else {
              const address = _.get(response, 'data.0')
              if (!_.isEmpty(address) && _.get(address, 'status') !== 'N') {
                this.addressToSubmit = this.convertDeliveryStoreToAddress(address)
                this.handleStoreChange()
              } else {
                //Delivery store not found
                this.submitError = this.trans('delivery_address_not_found')
              }
            }
          })
        .catch((err) => {
          this.submitting = false
          this.submitError = this.trans('could_not_get_delivery_address_please_try_again')
        })
      },
      convertDeliveryStoreToAddress(address) {
        const newAddress = {
          "sub_districts": {
            "id": _.get(this.address, 'original_subdistrict.id'),
            "name": _.get(this.address, 'subdistrict'),
            "original_name": {
              "th": _.get(this.address, 'original_subdistrict.th'),
              "en": _.get(this.address, 'original_subdistrict.en')
            }
          },
          "district": {
            "id": _.get(this.address, 'original_district.id'),
            "name": _.get(this.address, 'district'),
            "original_name": {
              "th": _.get(this.address, 'original_district.th'),
              "en": _.get(this.address, 'original_district.en')
            }
          },
          "province": {
            "id": _.get(this.address, 'original_province.id'),
            "name": _.get(this.address, 'province'),
            "original_name": {
              "th": _.get(this.address, 'original_province.th'),
              "en": _.get(this.address, 'original_province.en')
            }
          },
          "postcode": _.get(address, 'postcode'),
          "main_inventory_store": _.get(address, 'main_inventory_store'),
          "store_price": _.get(address, 'store_price'),
          "store_id": this.store_id
        }

        return newAddress
      },
      handleStoreChange() {
        //this.address.main_inventory_store
        //Check store id is changed
        const selectedStoreId = _.get(this.selected_address, 'main_inventory_store')
        const selectedStorePriceId = _.get(this.selected_address, 'store_price')
        if (this.addressToSubmit.main_inventory_store != selectedStoreId
          || this.addressToSubmit.store_price != selectedStorePriceId
          || this.returnToCart
        ) {
          //Check cart is empty or not
          if (this.cartCount < 1) {
            //Only update current store
            this.doUpdateCurrentStore()
          } else {
            //Confirm change address
            this.isConfirmChangeAddress = true
          }

        } else {
          //Do noting
          if (JSON.stringify(this.addressToSubmit) === JSON.stringify(this.selected_address)) {
            this.$store.commit(SET_OPEN_POPUP_ADDRESS, false)
          } else {
            this.doUpdateCurrentStore()
          }
        }
      },

      doUpdateCurrentStore() {
          this.submitting = true
          const payload = {
              store_id: this.addressToSubmit.main_inventory_store,
              store_price_id: this.addressToSubmit.store_price,
              delivery_type: this.deliveryTypes[this.shipping_type]
          }

        this.updateCurrentStore(payload)
          .then(() => {
            this.doSetSelectedAddress()
            this.$nextTick(() => {
              window.location.reload()
            })
          })
          .catch((err) => {
            this.submitting = false
            this.submitError = this.trans('update_current_store_error')
          })
      },

      onChangeAddressSuccess() {
        this.$nextTick(() => {
          window.location = this.$store.getters.locale_url + '/carts'
        })
      },

      onAddAddress() {
        this.editAddress = null
        this.showAddressForm = true
      },
      onCancelShowAddressForm() {
        this.showAddressForm = false
      },
      onAddAddressSuccess(address) {
        this.showAddressForm = false
                if (!this.editAddress) {
                    console.log(address)
                    this.address = address
                    this.$nextTick(() => {
                        this.$refs.delivery.scrollToBottom()
                        this.$refs.delivery.updateType('address')
                    })
                }
            },
            onEditAddress(address) {
                this.editAddress = address
                this.showAddressForm = true
            },
            getLangUrl(local) {
                let url = window.location.href
                if (local == 'en') {
                    return url.replace('/th', '/' + local)
                } else {
                    return url.replace('/en', '/' + local)
                }
            },
            onCancelConfirmChangeAddress() {
                this.isConfirmChangeAddress = false
            },
            cloneDataToSubmit() {
                this.addressToSubmit = _.cloneDeep(this.address)
                this.onSubmitConfirmChangeAddress()
            },
            onSubmitConfirmChangeAddress() {
                this.submitting = true
                this.submitError = ''

                const payload = {
                    store_id: this.addressToSubmit.main_inventory_store,
                    store_price_id: this.addressToSubmit.store_price,
                    delivery_type: this.deliveryTypes[this.shipping_type]
                }
                axios.post(`${this.$store.getters.locale_url}/carts/move-cart`, payload)
                    .then((response) => {
                        if (_.get(response, 'data.status') == 'success') {
                            this.doSetSelectedAddress()
                            this.onChangeAddressSuccess()
                            // if (_.get(response, 'data.items', []).length > 0) {
                            //     this.onChangeAddressSuccess()
                            // } else {
                            //     this.$nextTick(() => {
                            //         window.location.reload()
                            //     })
                            //
                            // }
                        } else {
                            this.submitting = false
                            this.submitError = _.get(response, 'data.message')
                        }

          })
          .catch((err) => {
            this.submitting = false
            this.submitError = this.trans('could_not_move_cart')
          })
      },

      doSetSelectedAddress() {
          this.$store.commit(UPDATE_PREVENT_MOVE_ACTIVE_ADDRESS_TO_TOP, true)
          let payload = this.addressToSubmit
          payload.id = _.get(this.address, 'id', null)
          payload.type = this.shipping_type
          this.setSelectedAddress(payload)
      },
      onCloseModal() {
          this.address = this.selected_address
          this.shipping_type = _.get(this.selected_address, 'type', '')
          this.$store.commit(SET_ERROR_STORE_NOTAVILABLE_MESSAGE, '')
      },
      onOpenModal() {
          this.getAddressFromApi()
          storeAddress.setOpenPopup(false)
          if (this.$refs.delivery) {
              this.$refs.delivery.initialData()
          }
          this.getActiveStore()
          if (this.$refs.pickup) {
            this.$refs.pickup.initialData()
          }

      },
      onBeforeOpenModal() {
        this.$refs.shippingAddressForm
        this.error_address_type = false
        this.submitError = ''
        if (this.$refs.delivery) {
          this.$refs.delivery.checkInitialData()
        }
      },
      changeLanguage(url) {
        storeAddress.setOpenPopup(true)
        this.$nextTick(() => {
          window.location = url
        })
      },
      getActiveStore() {
        this.submitting = true
        this.getActivePickupStoresFromApi().then(res => {
          this.activeStore = res.data
          if(_.get(this.selected_address, 'type') == 'store' && res.data.length == 0) {
            this.$store.commit(SET_ERROR_STORE_NOTAVILABLE_MESSAGE, trans('pickup_store_address_not_avilable'))
          }
          this.submitting = false
        }).catch(error => {
          this.submitting = false
          console.log(error)
        })
      }
    },
    mounted() {
      if (this.selected_address) {
        this.address = this.selected_address
        this.shipping_type = this.selected_address.type
      }
      this.getActiveStore()
    }
  }
</script>

<style scoped>

</style>