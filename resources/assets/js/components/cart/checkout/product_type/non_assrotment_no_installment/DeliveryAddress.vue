<template>
    <div>
        <button class="btn btn-primary" type="button" v-on:click="showAddressModal">Add address</button>

        <!--<div class="col-lg-6 col-md-6 col-sm-6 padding-right-ad margin-bottom-15">
            <div class="btn-group">
                <button type="button" class="btn-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Krit Shop, Krittinon Homklin, 76/1 moo. 13 soi. Tachan Rd. Suk Sawat, Nai…
                    <span class="form-control-Dropdown-B" aria-hidden="true"></span>
                </button>
                <ul class="dropdown-all">
                    <li><a href="#">Krit Shop, Krittinon Homklin, 76/1 moo. 13 soi. Tachan Rd. Suk Sawat, Nai…</a></li>
                    <li><a href="#">Krit Shop2, Krittinon Homklin, 76/1 moo. 13 soi. Tachan Rd. Suk Sawat, Nai…</a></li>
                    <li><a href="#">Krit Shop3, Krittinon Homklin, 76/1 moo. 13 soi. Tachan Rd. Suk Sawat, Nai…</a></li>
                </ul>
            </div>
        </div>-->
        <div class="clearfix"></div>

        <div class="col-lg-6 col-md-6 col-sm-6 pay-pickup padding-right-ad">

            <div class="address-box2" v-for="address in addresses">
                <div class="btn-er pull-right">
                    <button class="btn btn-default" type="button" v-on:click="showAddressModal">Edit</button>
                    <!--<button class="btn-remove2" data-toggle="modal" data-target="#myModal08">Remove</button>-->
                </div>
                {{ address.shop_name }} <br>
                {{ address.first_name }} {{ address.last_name }} <br>
                {{ address.address_line1 }} {{ address.address_line2 }}
                Rd. {{ address.road_street }}, {{ address.road_street }}, {{ address.city }}, {{ address.province }}, {{ address.postcode }} <br>
                <b>Tel.</b> {{ address.phone }}<br>
                <b>E-mail.</b> {{ address.email }}
            </div>

        </div>

        <div class="alert alert-danger" v-if="addresses.length < 1">
            Please add an address for delivery at home!
        </div>
        <div class="clearfix"></div>


        <!-- Add address modal -->
        <modal v-bind:show="isShowAddressModal" v-bind:additional-custom-classes="modalClasses" v-on:on-open-modal="onAddressModalOpen" v-on:on-close-modal="onAddressModalClose">
            <div slot="header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><b>Add address</b></h5>
            </div>
            <div>
                <form method="post" action="#" ref="add_delivery_address_form">
                    <cart-checkout-address input-name="delivery_home_address" v-bind:init-data="address"></cart-checkout-address>
                </form>
            </div>
            <div slot="footer">
                <div class="alert alert-danger" style="text-align: left;" v-if="errorValidate">
                    <strong>Could not add an address!</strong> Please check following error above.
                </div>
                <button type="button" class="btn btn-primary" v-on:click="submitAddAddress">Confirm</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </modal>
    </div>
</template>

<script>
    import 'jquery-validation'
    import Modal from '../../../../bootstrap/Modal'
    import cartCheckoutAddress from '../../address/Address'

    export default {
        name: 'cart-checkout-non-assortment-delivery-address',
        components: {
            'modal': Modal,
            'cart-checkout-address': cartCheckoutAddress
        },
        data: function () {
            return {
                modalClasses: {
                    'address-modal-box': true
                },
                isShowAddressModal: false,
                errorValidate: false,
                addresses: []
            }
        },
        methods: {
            onAddressModalOpen: function () {
                var me = this;
                var form = $(this.$refs.add_delivery_address_form);
                form.validate({
                    submitHandler: function (e) {
                        me.errorValidate = false;
                        me.onAddAddress(me.transformInputs(form.serializeArray()));
                        me.$emit('add-delivery-address', me.transformInputsArray(form.serializeArray()));
                        me.isShowAddressModal = false;
                        return false;
                    },
                    invalidHandler: function (result) {
                        me.errorValidate = true;
                        return false;
                    }
                });
            },
            onAddressModalClose: function () {
                this.isShowAddressModal = false
            },
            showAddressModal: function () {
                this.isShowAddressModal = true
            },
            submitAddAddress: function () {
                var form = $(this.$refs.add_delivery_address_form);
                form.trigger('submit');
            },
            transformInputs: function (data) {
                var transformedData = {};

                for (var i = 0; i < data.length; ++i) {
                    transformedData[data[i].name] = data[i].value;
                }
                return transformedData;
            },
            transformInputsArray: function (data) {
                var transformedData = [];

                for (var i = 0; i < data.length; ++i) {
                    transformedData.push({
                        'name': data[i].name,
                        'value': data[i].value
                    });
                }
                return transformedData;
            },
            onAddAddress: function (data) {
                var setData = {
                    'shop_name': data['delivery_home_address[shop_name]'],
                    'first_name': data['delivery_home_address[first_name]'],
                    'last_name': data['delivery_home_address[last_name]'],
                    'address_line1': data['delivery_home_address[address_line1]'],
                    'address_line2': data['delivery_home_address[address_line2]'],
                    'road_street': data['delivery_home_address[road_street]'],
                    'city': data['delivery_home_address[city]'],
                    'province': data['delivery_home_address[province]'],
                    'phone': data['delivery_home_address[phone]'],
                    'email': data['delivery_home_address[email]']
                };

                if (this.addresses.length > 0) {
                    this.addresses[0] = setData;
                } else {
                    this.addresses.push(setData);
                }
            },
            isAddAddress: function () {
                if (this.addresses.length < 1) {
                    return false;
                }

                return true;
            }
        },
        watch: {
            isShowAddressModal: function () {
                if (this.addresses.length < 1) {
                    var form = $(this.$refs.add_delivery_address_form);
                    $('input[type="text"], input[type="text"], input[type="number"]', form).val('');
                    $('select option:first-child', form).prop('selected', true);
                    $('label.error', form).remove();
                    this.errorValidate = false;
                }
            }
        },
        computed: {
            address: function () {
                if (this.addresses.length > 0) {
                    return this.addresses[0];
                }

                return null;
            }
        }
    }
</script>