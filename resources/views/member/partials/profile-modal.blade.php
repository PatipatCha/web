<div class="modal fade address-modal-box" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="profile-form" action="{{ route('members.profile.update') }}" method="post">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h5 class="modal-title"><b>{{ trans('frontend.edit_profile') }}</b></h5>
				</div>
				<div class="modal-body">
					<div class="form-group pay-pickup">
						<!---->
						<div class="col-sm-6 padding-right-ad">
							<label for="">{{ trans('frontend.username') }}</label>
							<input disabled type="text" class="form-control" id=""  placeholder="{{ trans('frontend.username') }}" value="{{ old('first_name', array_get($profile, 'username')) }}" data-rule-required="true" required="required">
						</div>
						<div class="col-sm-6 padding-left-ad">
							<label for="">{{ trans('frontend.shop_name') }}</label>
							<input type="text" class="form-control" id="" placeholder="{{ __('frontend.shop_name') }}" name="shop_name" value="{{ old('shop_name', array_get($address, 'address_name')) }}" data-rule-maxlength="100" data-msg-maxlength="{{ trans('frontend.validate_maxlength_shop_name') }}">
						</div>
						<!---->
						<div class="col-sm-6 padding-right-ad">
							<label for="">{{ trans('frontend.first_name') }}<span style="color:#F01616;"> *</span></label>
							<input type="text" class="form-control" id="" name="first_name" placeholder="{{ trans('frontend.first_name') }}" value="{{ old('first_name', array_get($profile, 'first_name')) }}" data-rule-required="true" required="required">
						</div>

						<div class="col-sm-6 padding-left-ad">
							<label for="">{{ trans('frontend.last_name') }}<span style="color:#F01616;"> *</span></label>
							<input type="text" class="form-control" id="" name="last_name" placeholder="{{ trans('frontend.last_name') }}" value="{{ old('last_name', array_get($profile, 'last_name')) }}" data-rule-required="true" required="required">
						</div>
						<!---->
						<div class="col-sm-6 padding-right-ad">
							<label for="">{{ trans('frontend.birth_day') }}<span style="color:#F01616;"> *</span></label>
							<div class=" form-calendar has-feedback">
								<date-picker input-name="birth_day" default-date="{{ old('birth_day', array_get($profile, 'birth_day')) }}" v-bind:required="true" v-bind:prevent-future="true"></date-picker>
							</div>
						</div>

						<div class="col-sm-6 padding-left-ad">
							<label for="">{{ trans('frontend.phone') }}<span style="color:#F01616;"> *</span></label>
							<input type="text" class="form-control" id="" placeholder="{{ __('frontend.mobile_phone') }}" name="phone" data-rule-required="true" value="{{ old('phone', array_get($profile, 'phone')) }}" required="required">
						</div>
						<!---->
						<div class="col-sm-12 no-padding">
							<label for="">{{ trans('frontend.email') }}</label>
							<input type="email" class="form-control email-field" id="" placeholder="{{ __('frontend.email_placholder') }}" name="email" data-rule-email="true" value="{{ old('email', array_get($profile, 'email')) }}">
						</div>
						<!---->
						

								<div class="col-sm-12 no-padding">
									<label for="">{{ __('frontend.address_line1') }}<span style="color:#F01616;"> *</span></label>
									<input name="address" value="{{ old('address', array_get($address, 'address')) }}" data-rule-required="true" data-rule-address_line1="true" required type="text" class="form-control" id="" placeholder="{{ __('frontend.home_moo_soi') }}">
								</div>

								{{--<div class="col-sm-6 padding-left-ad">
									<label for="">{{ __('frontend.address_line2') }}</label>
									<input name="address2" value="{{ old('address2', array_get($address, 'address2')) }}" type="text" class="form-control" id="" placeholder="{{ __('frontend.address') }}">
								</div>
								--}}
								<!---->
								<div class="col-sm-6 padding-right-ad">
									<label for="">{{ trans('frontend.province') }}<span style="color:#F01616;"> *</span></label>
									<div class="btn-group">
										<select class="form-control tax_province" name="province_id" data-rule-required="true" required>
											<option value="">{{ __('frontend.select_province') }}</option>
											@foreach($provinces as $province)
											<option {{ $province['name'] == array_get($address, 'province')?'selected="selected"':'' }} value="{{ $province['province_id'] }}">{{ $province['name'] }}</option>
											@endforeach
										</select>
										<label id="province_id-error" class="error" for="province_id" style="display: none;"></label>
									</div>
								</div>
								<div class="col-sm-6 padding-left-ad">
									<label for="">{{ trans('frontend.city') }}<span style="color:#F01616;"> *</span></label>
									<div class="btn-group">
										<select class="form-control tax_city" name="district_id" data-rule-required="true" required>
											<option value="">{{ __('frontend.select_district') }}</option>

											@foreach($districts as $district)
											<option {{ $district['name'] ==  array_get($address, 'district')?'selected="selected"':'' }} value="{{ $district['district_id'] }}">{{ $district['name'] }}</option>
											@endforeach
										</select>
										<label id="district_id-error" class="error" for="district_id" style="display: none;"></label>
									</div>
								</div>
								<div class="col-sm-6 padding-right-ad">
									<label for="">{{ trans('frontend.sub_district') }}<span style="color:#F01616;"> *</span></label>
									<div class="btn-group">
										<select class="form-control tax_sub_district" name="subdistrict_id" data-rule-required="true" required>
											<option value="">{{ __('frontend.select_subdistrict') }}</option>

											@foreach($subdistricts as $subdistrict)
											<option {{ $subdistrict['name'] == array_get($address, 'subdistrict')?'selected="selected"':'' }} value="{{ $subdistrict['sub_district_id'] }}">{{ $subdistrict['name'] }}</option>
											@endforeach
										</select>
										<label id="subdistrict_id-error" class="error" for="subdistrict_id" style="display: none;"></label>
									</div>
								</div>
								<!--<div class="col-sm-6">
									<label for="">{{ trans('frontend.sub_district') }}<span style="color:#F01616;"> *</span></label>
									<div class="btn-group">
										<div class="btn-group">
											<select class="form-control tax_sub_district" name="sub_district_id" data-rule-required="true" required>
												<option value="">-- Select Sub Districr --</option>
												{{--
												@foreach($subdistricts as $subdistrict)
													<option {{ $subdistrict['name'] == $address['subdistrict']?'selected="selected"':'' }} value="{{ $subdistrict['subdistrict_id'] }}">{{ $subdistrict['name'] }}</option>
												@endforeach
												--}}
											</select>
										</div>
									</div>
								</div>-->
								<!---->

								<!---->
								<div class="col-sm-6 padding-left-ad">
									<label for="">{{ trans('frontend.postcode') }}<span style="color:#F01616;"> *</span></label>
									<input value="{{ old('postcode', array_get($address, 'postcode')) }}" name="postcode" required type="text" class="form-control tax_postcode" id="" placeholder="{{ __('frontend.postcode_placeholder') }}" readonly>
									<?php if(!empty($address['id'])) {?>
									<input type="hidden" name="address_id" value="{{ $address['id'] }}"/>
									<?php }?>
								</div>
								<?php
								$lang = json_encode([
									'confirm' =>  __('frontend.confirm'),
									'confirm_verify_makro_card_id' => __('frontend.confirm_verify_makro_card_id'),
									'verify' => __('frontend.verify'),
									'please_enter_a_makro_card_id' =>  __('frontend.please_enter_a_makro_card_id'),
									'please_verify_makro_card_id' => __('frontend.please_verify_makro_card_id'),
									'makro_id_card' => __('frontend.makro_id_card'),
									'customer_name' => __('frontend.customer_name'),
									'invalid_makro_card_id' => __('frontend.invalid_makro_card_id'),
									'customer_info' =>  __('frontend.customer_info')
									]);

										$hasMakroCard = false;
										if (isset($profile['makro_card_id']) && !empty($profile['makro_card_id'])) {
                                            $hasMakroCard = true;
										}
									?>

									@if (!$hasMakroCard)
									<div class="col-sm-12 no-padding">
										<input type="hidden" name="old_makro_card_id" value="{{ old('makro_card_id', array_get($profile, 'makro_card_id')) }}"/>
										<makro-card-id-field v-bind:lang="{{ $lang }}" card-id="{{ old('makro_card_id', array_get($profile, 'makro_card_id')) }}" field-name="makro_card_id" :check-card="true"></makro-card-id-field>
										<span id="error-makro-id" style="color:red; display:none">{{ trans('frontend.verify_card_id') }}</span>
									</div>
									@endif
								<!--<div class="col-lg-12 no-padding">
									<div>
										<label for="">Makro Card ID</label>
									</div>
									<div class="box-MakroCardID pull-left">
										<input type="" class="form-control" id="" placeholder="101-999999-99-99">
									</div>
									<div class=" box-btn-verify pull-left">
										<span class="btn-verify">{{ trans('frontend.verify_card_id') }}</span>
									</div>
									<!--<label for="">{{ trans('frontend.makro_card_id') }}</label>
									<input type="text" class="form-control" id="" placeholder="1019999999999" name="makro_card_id" value="{{ old('makro_card_id', array_get($profile, 'makro_card_id')) }}">
									<button class="btn-verify" type="button" onclick="checkCardId">{{ trans('frontend.verify_card_id') }} </button>-->
									<!--</div>-->

									<div class="clearfix"></div>
								</div>

					@include('partials.notifications.error')

				</div>
				<div class="modal-footer">
					<button type="button" id="cancel-profile-btn" class="btn btn-default" data-dismiss="modal">
						<i class="far fa-close"></i> {{ trans('frontend.cancel') }}
					</button>
					<button type="btn" class="btn btn-primary pull-right" id="submit-profile-btn">
						<i class="far fa-check"></i> {{ trans('frontend.save') }}
					</button>
					{!! csrf_field() !!}
					{!! method_field('put') !!}
				</div>
			</form>
		</div>
	</div>
</div>


@section('script')
@parent

<script>

	$('.tax_province').change(function(){
		var id = $(this).val();
		var html = '<option value="">{{ __('frontend.select_district') }}</option>';
		$('.tax_city').html(html)
		var htmlsub = '<option value="">{{ __('frontend.select_subdistrict') }}</option>';
		$('.tax_sub_district').html(htmlsub)
		$.post('{{ route('addressgetCityById') }}',{province_id:id},function(data){

			for(var i = 0; i < data.length; i++){
				html += '<option value="'+ data[i].district_id +'">'+data[i].name+'</option>'
			}
			$('.tax_city').html(html)

		})
	})

	$('.tax_city').change(function(){
		var id = $(this).val();
		var html = '<option value="">{{ __('frontend.select_subdistrict') }}</option>';
		$('.tax_sub_district').html(html)
		$.post('{{ route('addressSubDistrictById') }}',{district_id:id},function(data){

			for(var i = 0; i < data.length; i++){
				html += '<option value="'+ data[i].sub_district_id +'">'+data[i].name+'</option>'
			}
			$('.tax_sub_district').html(html)

			$('.tax_sub_district').change(function(){
				var sub_id = $(this).val()
				$('.tax_postcode').val(getPostcode(sub_id, data))
			})


		})
	})

	function getPostcode(sub_id, data){
		for(var i = 0; i < data.length; i++){
			if(data[i].sub_district_id == sub_id){
				return data[i].postcode
			}
		}
	}

</script>
@stop