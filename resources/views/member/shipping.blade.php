@extends('layouts.main')

@section('nav-mobile-menu')

@endsection

@section('content')
<div class="container margin-bottom">
	<div class="row member-section">
		@include('member.partials.member-sidebar')
		<div class="col-lg-9 col-md-9 no-padding">
			<div class="deta-box3">
				<div class="topic-text">
					<b>
						{{ trans('frontend.my_shipping') }}
						<a tabindex="0" data-toggle="popover" data-trigger="focus hover" data-placement="bottom" data-content="{{ trans('frontend.shipping_address_type') }}">
							<!-- <img src="{{ asset('assets/images/icon-tips.png') }}" /> -->
							<i class="fas fa-info-circle"></i>
						</a>
					</b>
				</div>

				<div class="deta-box2">
					<shipping-address
							v-bind:addresses="{!! htmlspecialchars(json_encode($shipping_addresses)) !!}"
							:use-delivery-residence-address="true"
							shop-name-id="txt_shop_name"
							first-name-id="txt_first_name"
							last-name-id="txt_last_name"
							telephone-number-id="txt_mobile_number"
							email-id="txt_email"
							address-id="txt_address"
							province-id="cbo_province"
							district-id="cbo_district"
							sub-district-id="cbo_subdistrict"
							btn-save-id="btn_save"
							btn-cancel-id="btn_cancel"
							label-first-name="lbl_first_name"
							label-last-name="lbl_last_name"
							label-mobile-number="lbl_mobile_number"
							label-email="lbl_email"
							label-address="lbl_address"
							label-province="lbl_province"
							label-district="lbl_district"
							label-sub-district="lbl_subdistrict"
							label-postcode="lbl_postcode"
					></shipping-address>
					{{--<add-address>--}}
					{{--</add-address>--}}
					{{----}}
					{{----}}
					{{----}}
					{{--<shipping-display>--}}
					{{--</shipping-display>--}}
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	 <div class="clearfix"></div>
 </div>
</div>
@endsection

@section('script-header')
<script>
	var SHIPPING_DATA =  <?php echo json_encode($shipping) ?>
</script>

@endsection