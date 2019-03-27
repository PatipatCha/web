<template>
    <div>
        <!-- Has a Mark Card -->
        <div v-if="isCreatedSiebel || hasMakroCard || memberWasOrdered">
            <div class="pay-pickup-text text-bold">
                <b>{{ lang.tax_invoice_information }}</b>
            </div>

            <!-- Orange -->
            <div class="message-box" v-if="!isEmptyMakroCardInfo && makroCardInfoObj.makro_member_type == 'orange'">
                <p><b class="text-bold">{{ lang.company_name_personal_name }} :</b> {{ makroCardInfoObj ? makroCardInfoObj.request_response.CustName : '' }}</p>
                <p><b class="text-bold">{{ lang.head_office_branch_no }} :</b> {{ makroCardInfoObj ? makroCardInfoObj.request_response.CustBranch : '' }}</p>
                <p><b class="text-bold">{{ lang.tax_identification_number }} :</b> {{ makroCardInfoObj ? makroCardInfoObj.request_response.CustTax : '' }}</p>
                <p><b class="text-bold">{{ lang.tax_invoice_address }} :</b> <full-address v-bind:address="makroCardInfoObj.tax_address"></full-address></p>
                <!--<p>&nbsp;</p>-->
                <!--<p>{{ lang.billing_address }} : <full-address v-bind:address="makroCardInfo.billing_address"></full-address></p>-->
                <p>&nbsp;</p>
                <p>{{ lang.tax_invoice_green_type_notice_text }}</p>
            </div>

            <!-- Green -->
            <div class="message-box" v-if="isEmptyMakroCardInfo || makroCardInfoObj.makro_member_type == 'green'">
                <p><b class="text-bold">{{ lang.company_name_personal_name }} :</b> {{ makroCardInfoObj ? makroCardInfoObj.request_response.CustName : profile.business.shop_name }}</p>
                <p><b class="text-bold">{{ lang.head_office_branch_no }} :</b> {{ makroCardInfoObj ? makroCardInfoObj.request_response.CustBranch : profile.business.branch }}</p>
                <p><b class="text-bold">{{ lang.tax_identification_number }} :</b> {{ makroCardInfoObj ? makroCardInfoObj.request_response.CustTax : profile.tax_id }}</p>
                <p><b class="text-bold">{{ lang.tax_invoice_address }} :</b> <full-address v-bind:address="taxAddress"></full-address></p>
                <!--<p>&nbsp;</p>-->
                <!--<p>{{ lang.billing_address }} : <full-address v-bind:address="buyerAddress"></full-address></p>-->
                <p>&nbsp;</p>
                <p>{{ lang.tax_invoice_green_type_notice_text }}</p>
            </div>

        </div>

        <!-- No has a Mark Card -->
        <div v-if="!isCreatedSiebel && !hasMakroCard && !memberWasOrdered">
            <div class="pay-pickup-text text-bold"><b>{{ lang.tax_invoice_formation }}</b></div>
            <p>{{ lang.issue_tax_invoice }}</p>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="is_use_invoice_address" v-model="isUseInvoiceAddress" value="1" id="chk_tax_invoice"> {{ lang.require_full_tax_invoice }}
                </label>
            </div>

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding pull-right" v-if="isUseInvoiceAddress">
                <invoice-address input-name="invoice_address" v-bind:initData="address"></invoice-address>

                <!--<cart-checkout-billing-address v-bind:address="billingAddress"></cart-checkout-billing-address>-->
            </div>
        </div>

    </div>
</template>

<script>
    import CartCheckoutAddressInvoiceAddress from './address/InvoiceAddress'
    import cartCheckoutBillingAddress from  './BillingAddress'
    import FullAddress from './address/FullAddress'

    export default {
        name: 'cart-checkout-invoice-address',
        components: {
            'invoice-address': CartCheckoutAddressInvoiceAddress,
            'cart-checkout-billing-address': cartCheckoutBillingAddress,
            FullAddress
        },
        props: {
            address: {
                type: Object,
                default: null
            },
            billingAddress: {
                type: Object,
                default: null
            },
            makroCardInfo: {
                type: String,
                default: null
            },
            hasMakroCard: {
                type: Number,
                default: 0
            },
            isCreatedSiebel: {
                type: Number,
                default: 0
            },
            memberWasOrdered: {
                type: Number,
                default: 0
            },
            buyerAddress: {
                type: Object
            }
        },
        data: function () {
            return {
                isUseInvoiceAddress: false
            }
        },
        computed: {
            lang: function () {
                return this.$store.getters.lang;
            },
            isEmptyMakroCardInfo () {
                if (_.isEmpty(JSON.parse(this.makroCardInfo))) {
                    return true
                }

                return false
            },
            makroCardInfoObj() {
                return JSON.parse(this.makroCardInfo)
            },
            taxAddress() {
                if (this.address.tax_address === null) {
                    return this.buyerAddress
                }

                return this.address.tax_address
            },
            profile() {
                return this.address.profile
            }
        },
        mounted() {

        }
    }
</script>