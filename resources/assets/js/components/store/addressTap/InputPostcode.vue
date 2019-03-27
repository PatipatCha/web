<template>
    <div>
        <div class="input-group" :class="error_postcode?'has-error': ''" ref="inputGroup">
            <!--<input @keyup.enter="checkPostcode" type="text" class="form-control" v-model="postcode"/>-->
            <form v-on:submit.prevent="checkPostcode">
                <v-autocomplete
                        v-model="item"
                        :items="items"
                        :value="postcode"
                        :component-item='template'
                        @update-items="updateItems"
                        :auto-select-one-item="false"
                        :inputAttrs="{class:'form-control post-code-input',  autocomplete:'off', name: 'postcode', id: 'txt_popup_search_delivery_by_postcode', value: postcode}"
                        @item-clicked="selectAddressFromAuto"
                        :get-label="getLabel"
                        :min-len="1"
                        @blur="inputPostcode"
                        @input="inputPostcode"
                        :keep-open="keepOpen"
                        @focus="onFocus"
                >
                </v-autocomplete>
            </form>
            <div v-if="error_postcode" class="remark">
                <i class="fas fa-exclamation-triangle"></i> {{ error_postcode_message }}
            </div>
            <span class="input-group-btn">
                      <button id="btn_popup_verify_postcode" :disabled="loading" type="button" @click="checkPostcode"
                              class="btn btn-default">
                        <i class="far fa-check" v-if="!loading"></i> <loading :show="loading"></loading> {{ trans('check') }}
                      </button>
                    </span>
        </div>
    </div>
</template>

<script>
    import {trans} from "../../../libraries/trans"
    import Loading from '../../loading/Loading'
    import AddressItem from './AddressItem'
    import 'jquery-validation'

    export default {
        name: "InputPostcode",
        components: {
            Loading,
            AddressItem
        },
        props: {
            addressType: {
                type: String,
                default: '',
            },
            onSelectAddress: {
                type: Function,
                default: () => {
                }
            },
            onUpdatePostcode: {
                type: Function,
                default: () => {
                }
            }
        },
        data() {
            return {
                error_postcode_message: trans('post_code_invalid'),
                postcode: '',
                error_postcode: false,
                loading: false,
                item: null,
                items: [],
                address_type: this.addressType,
                template: AddressItem,
                keepOpen: false
            }
        },
        computed: {
            addresses: function () {
                return this.$store.state.storeModule.addresses
            }
        },
        methods: {
            checkPostcode() {
                // console.log(34)
                this.error_postcode = false
                // var regex = /[0-9]{5}/;
                $('.special-char-error').remove()
                let regex = /^[0-9]*$/;
                if (regex.test(this.postcode) && this.postcode.length > 5) {
                    this.error_postcode = true
                    this.error_postcode_message = trans('error_zip_code_length')
                } else {
                    this.error_postcode = false
                }
                if(!this.postcode) {
                    this.error_postcode = true
                    this.error_postcode_message = trans('error_zip_code_length')
                }
                if (!this.error_postcode && this.postcode.length > 2) {
                    this.loading = true
                    axios.get(`${this.$store.getters.locale_url}` + '/address/delivery-by-postcode?postcode=' + `${this.postcode}`).then(response => {
                        let address = response.data
                        if (address.length <= 0) {
                            this.error_postcode = true
                            this.error_postcode_message = trans('delivery_address_not_found')
                            this.loading = false
                        } else {
                            this.error_postcode = false
                            this.address_type = 'postcode'
                            let payload = {
                                address: _.first(address),
                                type: this.address_type
                            };
                            this.postcode = payload.address.postcode
                            this.inputPostcode(this.postcode)
                            this.onSelectAddress(payload)
                            this.loading = false
                        }
                    }).catch(error => {
                        this.loading = false
                    })
                    // this.address_type = 'postcode'
                }

            },
            updateItems(text) {
                this.keepOpen = true
                this.postcode = text
                // this.onUpdatePostcode(text)
                var regex = /^[0-9]*$/;
                if (regex.test(this.postcode) && this.postcode.length > 5) {
                    this.error_postcode = true
                    this.error_postcode_message = trans('error_zip_code_length')
                } else {
                    this.error_postcode = false
                }

                if (!this.error_postcode && text.length > 2) {
                    // this.loading = true
                    axios.get(`${this.$store.getters.locale_url}` + '/address/delivery-by-postcode?postcode=' + `${this.postcode}`).then(response => {
                        // let address = response.data
                        // if (address.length <= 0) {
                        //     this.error_postcode = true
                        //     this.error_postcode_message = trans('address_not_found_search')
                        // } else {
                        this.items = response.data
                        // }
                        // this.loading = false
                    }).catch(error => {
                        // this.loading = false
                    })
                    // this.address_type = 'postcode'
                }
            },
            getLabel(item) {
                this.postcode = _.get(item, 'postcode', '')
                return _.get(item, 'postcode', '')
            },
            selectAddressFromAuto(address) {
                console.log(_.isEmpty(address))
                if (!_.isEmpty(address)) {
                    this.error_postcode = false
                    this.address_type = 'postcode'
                    let payload = {
                        address: address,
                        type: this.address_type
                    };
                    console.log(payload)
                    this.onSelectAddress(payload)
                }
            },
            updatePostcode(type, address) {
                this.postcode = address.postcode
                this.address_type = type
                this.item = address
                $('input', $(this.$refs.inputGroup)).val(address.postcode)
            },
            inputPostcode(e) {
                console.log('input')
                this.postcode = e
                this.onUpdatePostcode(e)
            },
            onFocus() {
                console.log('focus')
                if (this.items && this.items.length > 0) {
                    console.log('keepopen', true)
                    this.keepOpen = true
                }
            }
        },
        watch: {
            address_type: function () {
                if (this.address_type === 'address') {
                    // this.postcode = ''
                    this.error_postcode = false
                }
            },
            keepOpen: function (value) {
                console.log('watch keepOpen', value)
            }
        },
        mounted() {
            // $('.post-code-input').validate()
            $(document).on('click', (e) => {
                if (!e.target.className.match(/post\-code\-input/)) {
                    this.keepOpen = false
                }
            })
        }
    }
</script>

<style scoped>

</style>