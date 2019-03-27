<template>
	<div class="form-group pay-pickup">
		<!---->
		<div class="col-sm-12 no-padding">
			<label for="txt_tax_personal_company_name">{{ lang.company_name_personal_name }}<span style="color:#F01616;"> *</span></label>
			<input type="text" class="form-control"  :placeholder="lang.company_name_personal_name" v-bind:name="getInputName('company_name')"  v-model="company_name"  data-rule-required="true" data-rule-maxlength="100" :data-msg-maxlength="lang.validate_maxlength_company_name_personal_name" id="txt_tax_personal_company_name">
			<label class="error" for="txt_tax_personal_company_name" id="lbl_tax_personal_company_name"></label>
		</div>
		<!---->
		<div class="col-sm-6 padding-right-ad tooltip01">
			<label for="txt_tax_id">{{ lang.tax_identification_number }}<span style="color:#F01616;"> *</span></label>
			<div class="has-feedback">
				<input type="text" class="form-control" :placeholder="lang.tax_id" v-bind:name="getInputName('tax_id_number')" v-model="tax_id"  data-rule-required="true" required data-rule-number="true" data-rule-maxlength="13" data-rule-minlength="13" :data-msg-maxlength="lang.validate_maxlength_tax_identification_number" :data-msg-minlength="lang.validate_minlength_tax_identification_number" id="txt_tax_id">
				<span class="form-control-tips" id="tooltip" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" v-bind:title="lang.invoice_address_tax_id_filed_tooltip"></span>
			</div>
			<label class="error" for="txt_tax_id" id="lbl_tax_id"></label>
		</div>

		<div class="col-sm-6 padding-left-ad tooltip01">
			<label for="txt_tax_branch_no">{{ lang.head_office_branch_no }}<span style="color:#F01616;"> *</span></label>
			<div class="has-feedback">
				<input type="text" class="form-control" :placeholder="lang.head_office_branch_no" v-bind:name="getInputName('branch_no')" v-model="branch_no"  data-rule-required="true" required data-rule-digits="true" data-rule-minlength="5" data-rule-maxlength="5" :data-msg-maxlength="lang.head_office_branch_no_min_max_length" :data-msg-minlength="lang.head_office_branch_no_min_max_length" id="txt_tax_branch_no">
				<span class="form-control-tips" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" v-bind:title="lang.invoice_address_branch_no_field_tooltip"></span>
			</div>
			<label class="error" for="txt_tax_branch_no" id="lbl_tax_branch_no"></label>
		</div>
		<!---->
		<!-- <div class="col-sm-6 padding-right-ad">
			<label for="">{{ lang.telephone_number_label }}<span style="color:#F01616;"> *</span></label>
			<input type="text" class="form-control" :placeholder="lang.mobile_phone" v-bind:name="getInputName('phone')" v-model="phone" data-rule-minlength="10" data-rule-maxlength="10" data-rule-required="true" data-rule-number="true" required  :data-msg-maxlength="lang.validate_maxlength_telephone_number" :data-msg-minlength="lang.validate_minlength_telephone_number">
		</div>

		<div class="col-sm-6 padding-left-ad">
			<label for="">{{ lang.e_mail_label }}</label>
			<input type="text" class="form-control" :placeholder="lang.email_placholder" v-bind:name="getInputName('email')" v-model="email" data-rule-maxlength="150" data-rule-email="true"> -->
		<!-- </div> -->
		<!---->
		<residence-address
				input-name="invoice_address"
				v-bind:init-data="initData.tax_address"
				address-id="txt_tax_address"
				province-id="cbo_tax_province"
				district-id="cbo_tax_district"
				sub-district-id="cbo_tax_subdistrict"
				postcode-id-name="cbo_tax_postcode"
				label-address="lbl_tax_address"
				label-province="lbl_tax_province"
				label-district="lbl_tax_district"
				label-sub-district="lbl_tax_subdistrict"
				label-postcode="lbl_tax_postcode"
				ignore-validate-class="first-do-not-ignore"
		></residence-address>
		<!---->
		<div class="clearfix"></div>
	</div>
</template>

<script>
	import ResidenceAddress from './Residence'

	export default {
		name: 'cart-checkout-invoice-address',
		components: {
			ResidenceAddress
		},
		props: {
			inputName: {
				type: String,
				required: true
			},
			initData: {
				type: Object,
				default: null
			}
		},
		data: function () {
			return {
				company_name: this.getShopName(),
				tax_id: this.getTaxId(),
				branch_no: this.getBranch(),
				phone: this.getPhone(),
				email: this.getEmail()
			}
		},
		methods: {
			getInputName: function (name) {
				return this.inputName + '[' + name + ']';
			},
			getShopName: function () {
				if (typeof this.initData == 'object' && this.initData && (typeof this.initData.profile == 'object' && this.initData.profile)) {
					return this.initData.profile.business.shop_name;
				}

				return '';
			},
			getTaxId: function () {
				if (typeof this.initData == 'object' && this.initData && (typeof this.initData.profile == 'object' && this.initData.profile)) {
					return this.initData.profile.tax_id;
				}

				return '';
			},
			getBranch: function () {
				if (typeof this.initData == 'object' && this.initData && (typeof this.initData.profile == 'object' && this.initData.profile)) {
					return this.initData.profile.business.branch;
				}

				return '';
			},
			getFirstName: function () {
				if (typeof this.initData == 'object' && this.initData) {
					if (typeof this.initData.first_name == 'string' ) {
						return this.initData.first_name
					}
				}

				return '';
			},
			getLastName: function () {
				if (typeof this.initData == 'object' && this.initData) {
					if (typeof this.initData.last_name == 'string' ) {
						return this.initData.last_name
					}
				}


				return '';
			},
			getPhone: function () {
				if (typeof this.initData == 'object' && this.initData && (typeof this.initData.profile == 'object' && this.initData.profile)) {
					return this.initData.profile.business.mobile_phone;
				}

				return '';
			},
			getEmail: function () {
				if (typeof this.initData == 'object' && this.initData && (typeof this.initData.profile == 'object' && this.initData.profile)) {
					return this.initData.profile.business.email;
				}

				return '';
			}
		},
		computed: {
			lang: function () {
				return this.$store.getters.lang;
			}
		},
		mounted: function () {
            $('[data-toggle="tooltip"]').tooltip({
                trigger: 'hover focus click',
                html: true,
                container: 'body'
			});

            $('body').on('click', function (e) {
                $('[data-toggle="tooltip"]').each(function () {
                    //the 'is' for buttons that trigger popups
                    //the 'has' for icons within a button that triggers a popup
                    if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.tooltip').has(e.target).length === 0) {
                        $(this).tooltip('hide');
                    }
                });
            });
        }
	}
</script>