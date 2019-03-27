<template>
    <div>
        <button type="button" class="btn btn-success" v-on:click="showForm">Add Address</button>

        <modal v-bind:show="showModal" v-on:on-close-modal="onCloseModal" additional-custom-classes="address-modal-box">

            <div slot="header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-right: 0;"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <slot name="title">
                        Add Address
                    </slot>
                </h4>
            </div>


            <div>
                <div class="form-group pay-pickup">
                    <div class="col-lg-12 no-padding">
                        <label for="">Shop Name <span style="color:#F01616;"> *</span></label>
                        <input type="text" class="form-control" v-bind:name="getInputName('shop_name')" placeholder="Shop Name" >
                    </div>
                    <!---->
                    <div class="col-lg-6 col-md-6 col-sm-6 padding-right-ad">
                        <label for="">Name<span style="color:#F01616;"> *</span></label>
                        <input type="text" class="form-control" v-bind:name="getInputName('first_name')" placeholder="Name" data-rule-required="true" required>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-6 padding-left-ad">
                        <label for="">Surname<span style="color:#F01616;"> *</span></label>
                        <input type="text" class="form-control" v-bind:name="getInputName('last_name')" placeholder="Surname" data-rule-required="true" required>
                    </div>
                    <!---->

                    <div class="col-lg-6 col-md-6 col-sm-6 padding-right-ad">
                        <label for="">Telephone Number<span style="color:#F01616;"> *</span></label>
                        <input type="text" class="form-control" v-bind:name="getInputName('phone')" placeholder="Telephone Number">
                    </div>
                    <!---->
                    <div class="col-lg-6 col-md-6 col-sm-6 padding-left-ad">
                        <label for="">E-mail</label>
                        <input type="text" class="form-control" v-bind:name="getInputName('email')" placeholder="sample@sample.com" :data-rule-email="true">
                    </div>
                    <!---->

                    <residence-address></residence-address>


                    <div class="clearfix"></div>
                </div>
            </div>


            <div slot="footer">
                <button type="button" class="btn btn-primary">Confirm</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

            </div>
        </modal>
    </div>
</template>

<script>
    import Modal from '../../../bootstrap/Modal'
    import ResidenceAddress from '../address/Residence'

    export default {
        name: 'shipping-address-add',
        components: {
            Modal,
            ResidenceAddress
        },
        data: function () {
            return {
                showModal: false
            }
        },
        methods: {
            onCloseModal: function () {
                this.showModal = false;
            },
            showForm: function () {
                this.showModal = true;
            },

            getInputName: function () {
                return 'test'
            }
        },
        computed: {
            provinces: function () {
                return this.$store.state.appModule.provinces
            },
            cities: function () {
                return this.$store.state.appModule.cities
            },
            sub_districts: function () {
                return this.$store.state.appModule.sub_districts
            }
        },
    }
</script>