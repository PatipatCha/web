<template>
    <div class="item" :class="column">
        <div class="message-box" @click="selectItem" :class="messageBoxClass">
            <div class="radio input_shipping_address_ids" v-if="selectable">
                <label v-bind:for="'address-1' + item.id">
                    <input type="radio" name="shipping_address_id" v-bind:value="item.id" v-model="shipping_address_id" class="input_shipping_address_ids first-do-not-ignore" v-on:click="selectShippingAddress">
                </label>
            </div>
            <address>
                <p><strong class="text-bold" v-if="item.address_name && item.address_name.length">{{ item.address_name }}</strong></p>
                {{ item.first_name }} {{ item.last_name }}<br>
                <!--<p><b>Tax ID.</b> 13131313131</p>-->
                <b class="text-bold">{{ lang.address }} : </b><full-address v-bind:address="item"></full-address><br>
                <b class="text-bold">{{ lang.tel }} : </b>{{ item.contact_phone }}<br>
                <b class="text-bold">{{ lang.e_mail }} : </b>{{ item.contact_email }}
            </address>
            <div class="action">
                <button type="button" class="btn btn-default btn-xs edit-btn">{{ lang.edit }}</button>
                <button type="button" class="btn btn-default btn-xs delete-btn" v-if="canDelete">{{ lang.remove }}</button>
            </div>
        </div>
    </div>
</template>

<script>
    import FullAddress from '../address/FullAddress'
    import popup from '../../../../libraries/popup'

    export default {
        name: 'shipping-address-list-item',
        props: {
            item: {
                type: Object
            },
            index: {
                type: Number
            },
            selectable: {
                type: Boolean,
                default: false
            },
            selectedShippingAddressId: {
                type: String
            },
            column: {
                type: String,
                default: 'col-sm-6 item'
            },
            canDelete: {
                type: Boolean,
                default: true
            },
            selectAddressLabel: {
                type: String,
                default: ''
            },
            activeClass: {
                type: Boolean,
                default: false
            }
        },
        components: {
            FullAddress
        },
        data: function () {
            return {
                shipping_address_id: this.selectedShippingAddressId
            }
        },
        methods: {
            edit: function () {
                this.$emit('on-edit', this.index)
            },
            remove: function () {
                var $this = this;

                // popup.open(this.lang.are_you_sure, this.lang.to_remove_this_address, 'confirm', true, function (done) {
                //     done()
                    $this.$emit('on-remove', $this.index)
                    // popup.close()
                    //
                    // $.toast({
                    //     heading: $this.lang.your_shipping_address_has_been_removed,
                    //     position: 'top-right',
                    //     icon: 'success'
                    // });
                // })
            },
            selectShippingAddress: function () {
                if ($('#other-address').length) {
                    $('#other-address').prop('checked', true);
                }

                this.$emit('select-shipping-address', this.item.id)
            },
            selectItem(e) {
                if ($(e.target).hasClass('edit-btn')) {
                    this.edit()
                } else  if ($(e.target).hasClass('delete-btn')) {
                    this.remove()
                } else {
                    this.$emit('select-shipping-address', this.item.id)
                    $('input[type="radio"]', $(e.target).closest('.message-box')).click()

                }
            }
        },
        computed: {
            lang: function () {
                return this.$store.getters.lang
            },
            getSelectAddressLabel() {
                if (this.selectAddressLabel == 'cart') {
                    return this.$store.getters.lang.select_this_delivery_address
                }

                return this.$store.getters.lang.select_this_address
            },
            messageBoxClass() {
                let active = false
                if (this.selectedShippingAddressId == this.item.id
                    && this.activeClass === true
                ) {
                    active = true
                }

                return {
                    'active': active
                }
            }
        },
        watch: {
            selectedShippingAddressId: function (newValue) {
                console.log('selectedShippingAddressId')
                this.$emit('select-shipping-address', newValue)
                this.shipping_address_id = newValue
            },
            shipping_address_id: function (newValue) {
                console.log('shipping_address_id', newValue)
                this.$emit('select-shipping-address', newValue)
            }
        },
        mounted() {
            if (this.shipping_address_id == this.item.id) {
                this.$emit('select-shipping-address', this.item.id)
            }
        }
    }
</script>