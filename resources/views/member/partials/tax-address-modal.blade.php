<div class="modal fade address-modal-box" id="editTaxAddressModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form action="{{ route('members.tax.update') }}" method="post" id="tax-form">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h5 class="modal-title"><b>
						{{ trans('frontend.add') }} / {{ trans('frontend.edit') }}
						{{ trans('frontend.tax_address') }}</b></h5>
					</div>

					<div class="modal-body">
						<div class="form-group pay-pickup">
							<div class="col-sm-12 no-padding">
								<label for="">{{ trans('frontend.company_name') }} / {{ trans('frontend.corporate_name') }}<span style="color:#F01616;"> *</span></label>
								<input name="tax_shop_name" value="{{ old('tax_shop_name', array_get($profile, 'business.shop_name')) }}"  required type="text" class="form-control" id="" placeholder="{{ trans('frontend.company_name') }} / {{ trans('frontend.corporate_name') }}">
							</div>
							<!---->
							<div class="col-sm-6 padding-right-ad">
								<label for="">{{ trans('frontend.tax_identification_number') }}<span style="color:#F01616;"> *</span></label>
								<input name="tax_tax_id" value="{{ old('tax_tax_id', array_get($profile, 'tax_id')) }}"  required type="text" class="form-control" id="" placeholder="{{ trans('frontend.tax_identification_number') }}">
							
								</div>

							<div class="col-sm-6 padding-left-ad">
								<label for="">{{ trans('frontend.headquarters') }}/{{ trans('frontend.branch') }}<span style="color:#F01616;"> *</span></label>
								<input name="tax_branch" value="{{ old('tax_branch', array_get($profile, 'business.branch')) }}"  required type="text" class="form-control" id="" placeholder="{{ trans('frontend.headquarters') }}/{{ trans('frontend.branch') }}">
							</div>
							<!---->
							<div class="col-sm-6 padding-right-ad">
								<label for="">{{ trans('frontend.mobile_phone') }}<span style="color:#F01616;"> *</span></label>
								<input name="tax_mobile_phone" value="{{ old('tax_mobile_phone', array_get($profile, 'business.mobile_phone')) }}"  required type="text" class="form-control" id="" placeholder="{{ trans('frontend.mobile_phone') }}">
							</div>

							<div class="col-sm-6 padding-left-ad">
								<label for="">{{ trans('frontend.email') }}</label>
								<input name="tax_email" value="{{ old('tax_email', array_get($profile, 'business.email')) }}"  type="email" class="form-control" id="" placeholder="{{ trans('frontend.email') }}">
							</div>
							<!---->
							<div class="col-sm-6 padding-right-ad">
								<label for="">{{ trans('frontend.address_line1') }}<span style="color:#F01616;"> *</span></label>
								<input name="tax_address" value="{{ old('tax_address', array_get($taxAddress, 'address')) }}"  required type="text" class="form-control" id="" placeholder="{{ trans('frontend.home_moo_soi') }}">
							</div>

							{{--<div class="col-sm-6 padding-left-ad">
								<label for="">{{ trans('frontend.address_line2') }}</label>
								<input name="tax_address2" value="{{ old('tax_address2', array_get($taxAddress, 'address2')) }}"  type="text" class="form-control" id="" placeholder="{{ trans('frontend.address') }}">
							</div>--}}
							<!---->
							<div class="col-sm-6 padding-left-ad">
								<label for="">{{ trans('frontend.province') }}<span style="color:#F01616;"> *</span></label>
								<div class="btn-group">
									<select class="form-control tax_province" name="tax_province_id" data-rule-required="true" required>
										<option value="">{{ __('frontend.select_province') }}</option>
										@foreach($provinces as $province)
										<option {{ $province['name'] == array_get($taxAddress, 'province')?'selected="selected"':'' }} value="{{ $province['province_id'] }}">{{ $province['name'] }}</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class="col-sm-6 padding-right-ad">
								<label for="">{{ trans('frontend.city') }}<span style="color:#F01616;"> *</span></label>
								<div class="btn-group">
									<select class="form-control tax_city" name="tax_district_id" data-rule-required="true" required>
										<option value="">{{ __('frontend.select_district') }}</option>
										@foreach($tax_districts as $district)
										<option {{ $district['name'] == array_get($taxAddress, 'district')?'selected="selected"':'' }} value="{{ $district['district_id'] }}">{{ $district['name'] }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-sm-6 padding-left-ad">
								<label for="">{{ trans('frontend.sub_district') }}<span style="color:#F01616;"> *</span></label>
								<div class="btn-group">
									<select class="form-control tax_sub_district" name="tax_sub_district_id" data-rule-required="true" required>
										<option value="">{{ __('frontend.select_subdistrict') }}</option>
										@foreach($tax_subdistricts as $subdistrict)
										<option {{ $subdistrict['name'] == array_get($taxAddress, 'subdistrict')?'selected="selected"':'' }} value="{{ $subdistrict['sub_district_id'] }}">{{ $subdistrict['name'] }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<!---->

							<!---->
							<div class="col-sm-6 padding-right-ad">
								<label for="">{{ trans('frontend.postcode') }}<span style="color:#F01616;"> *</span></label>
								<input value="{{ old('tax_postcode', array_get($taxAddress, 'postcode')) }}"  name="tax_postcode" required type="text" class="form-control tax_postcode" id="" placeholder="{{ __('frontend.postcode') }}" readonly>
							</div>
							<!---->
							<div class="clearfix"></div>


							<!-- ที่อยู่จัดส่งใจเสร็จ -->
							<div class="col-sm-12 no-padding">
								<label style="cursor:pointer"><input {{ $is_use_tax == true ? 'checked="checked"':'' }} name="use_billing_address" type="checkbox" class="use_billing_address"> {{ trans('frontend.delivery_receipt_use_the_same_address_as_the_tax_invoice') }}</label>
							</div>
							<div class="bill_address" style="display: {{ $is_use_tax == true ? 'none':'block' }}">
								<div class="flex-item">
									<div class="col-sm-12 no-padding">
										<label for="">{{ trans('frontend.shop_name') }}</label>
										<input name="bill_shop_name" value="{{ old('tax_shop_name', array_get($billAddress, 'address_name')) }}"  type="text" class="form-control" id="" placeholder="{{ trans('frontend.shop_name') }}">
									</div>
									<div class="col-sm-6 padding-right-ad">
										<label for="">{{ trans('frontend.first_name') }}</label>
										<input value="{{ old('bill_first_name', array_get($billAddress, 'first_name')) }}"  type="text" name="bill_first_name" class="form-control" id="" placeholder="{{ trans('frontend.first_name') }}">
									</div>

									<div class="col-sm-6 padding-left-ad">
										<label for="">{{ trans('frontend.last_name') }}</label>
										<input value="{{ old('bill_last_name', array_get($billAddress, 'last_name')) }}"  type="text" name="bill_last_name" class="form-control" id="" placeholder="{{ trans('frontend.last_name') }}">
									</div>
									<!---->
									<div class="col-sm-6 padding-right-ad"></label>
										<label for="">{{ trans('frontend.mobile_phone') }}</label><span style="color:#F01616;"> *</span>
										<input name="bill_mobile_phone" value="{{ old('bill_mobile_phone', array_get($billAddress, 'contact_phone')) }}" type="text" class="form-control bill_form" id="" placeholder="{{ trans('frontend.mobile_phone') }}">
									</div>

									<div class="col-sm-6 padding-left-ad">
										<label for="">{{ trans('frontend.email') }}
										<input name="bill_contact_email" value="{{ old('bill_email', array_get($billAddress, 'contact_email')) }}"  required type="email" class="form-control" id="" placeholder="{{ trans('frontend.email') }}">
									</div>
									<!---->
									<div class="col-sm-6 padding-right-ad">
										<label for="">{{ trans('frontend.address_line1') }}<span style="color:#F01616;"> *</span></label>
										<input required value="{{ old('bill_address', array_get($billAddress, 'address')) }}"  name="bill_address" type="text" class="form-control bill_form" id="" placeholder="{{ trans('frontend.home_moo_soi') }}">
									</div>

									{{--<div class="col-sm-6 padding-left-ad">
										<label for="">{{ trans('frontend.address_line2') }}</label>
										<input value="{{ old('bill_address2', array_get($billAddress, 'address2')) }}"  name="bill_address2" type="text" class="form-control" id="" placeholder="{{ trans('frontend.address') }}">
									</div>--}}
									<!---->

									<!---->
									<div class="col-sm-6 padding-left-ad">
										<label for="">{{ trans('frontend.province') }}<span style="color:#F01616;"> *</span></label>
										<div class="btn-group">
											<select required class="form-control bill_province  bill_form select2" name="bill_province_id">
												<option value="">{{ __('frontend.select_province') }}</option>
												@foreach($provinces as $province)
													<option {{ $province['name'] == array_get($billAddress, 'province')?'selected="selected"':'' }} value="{{ $province['province_id'] }}">{{ $province['name'] }}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="col-sm-6 padding-right-ad">
										<label for="">{{ trans('frontend.city') }}<span style="color:#F01616;"> *</span></label>
										<div class="btn-group">
											<select required class="form-control bill_district  bill_form select2" name="bill_district_id" >
												<option value="">{{ __('frontend.select_district') }}</option>
												@foreach($bill_districts as $district)
													<option {{ $district['name'] == array_get($billAddress, 'district')?'selected="selected"':'' }} value="{{ $district['district_id'] }}">{{ $district['name'] }}</option>
												@endforeach
											</select>
										</div>
									</div>

									<div class="col-sm-6 padding-left-ad">
										<label for="">{{ trans('frontend.sub_district') }}<span style="color:#F01616;"> *</span></label>
										<div class="btn-group">
											<select required class="form-control bill_sub_district  bill_form select2" name="bill_sub_district_id" data-rule-required="true" required>
												<option value="">{{ __('frontend.select_subdistrict') }}</option>

												@foreach($bill_subdistricts as $subdistrict)
													<option {{ $subdistrict['name'] == array_get($billAddress, 'subdistrict')?'selected="selected"':'' }} value="{{ $subdistrict['sub_district_id'] }}">{{ $subdistrict['name'] }}</option>
												@endforeach
											</select>
										</div>
									</div>
									<!---->

									<!---->
									<div class="col-sm-6 padding-right-ad">
										<label for="">{{ trans('frontend.postcode') }}<span style="color:#F01616;"> *</span></label>
										<input required value="{{ old('bill_postcode', array_get($billAddress, 'postcode')) }}"  type="text" name="bill_postcode" class="form-control bill_postcode  bill_form" id="" placeholder="{{ __('frontend.postcode') }}" readonly>

									</div>

								</div>
							</div>

							<?php if(!empty($taxAddress['id'])){  ?>
							<input type="hidden" name="tax_address_id" value="{{ $taxAddress['id'] }}"/>
							<?php	} ?>

							<?php if(!empty($billAddress['id'])){  ?>
							<input type="hidden" name="bill_address_id" value="{{ $billAddress['id'] }}"/>
							<?php	} ?>
							<!---->
							<div class="clearfix"></div>
						</div>
					</div>

					<div class="modal-footer">
						<button type="submit" class="btn btn-primary">{{ trans('frontend.save') }}</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('frontend.cancel') }}</button>
						{!! csrf_field() !!}
						{!! method_field('put') !!}
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="modal fade address-modal-box" id="removeTaxAddressModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form action="{{ route('members.taxRemove') }}" method="post">
					<div class="modal-body">
						<?php if(!empty($taxAddress['id'])){  ?>
						<input type="hidden" name="tax_address_id" value="{{ $taxAddress['id'] }}"/>
						<?php	} ?>

						<?php if(!empty($billAddress['id'])){  ?>
						<input type="hidden" name="bill_address_id" value="{{ $billAddress['id'] }}"/>
						<?php	} ?>
						{{ csrf_field() }}
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<div class="text-modal-remove">
							<b>You are sure to remove this address ?</b>
						</div>
					</div>

					<div class="modal-footer">
						<button type="submit" class="btn btn-primary">Confirm</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	@section('script-header')
	@parent

	<link href="{{ asset('js/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
	@stop

	@section('script')
	@parent

	<script type="text/javascript" src="{{ asset('js/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
	<script>
		$(function() {
			$('#birth_day_picker').datepicker({
				format: 'yyyy-mm-dd'
			});
		})
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

		$('.bill_province').change(function(){
			var id = $(this).val();
			var html = '<option value="">{{ __('frontend.select_district') }}</option>';
			$('.bill_district').html(html)

			var htmlsub = '<option value="">{{ __('frontend.select_subdistrict') }}</option>';
			$('.bill_sub_district').html(htmlsub)

			$.post('{{ route('addressgetCityById') }}',{province_id:id},function(data){
				
				for(var i = 0; i < data.length; i++){
					html += '<option value="'+ data[i].district_id +'">'+data[i].name+'</option>'
				}
				$('.bill_district').html(html)
				
			})
		})

		$('.bill_district').change(function(){
			var id = $(this).val();
			var html = '<option value="">{{ __('frontend.select_subdistrict') }}</option>';
			$('.bill_sub_district').html(html)
			$.post('{{ route('addressSubDistrictById') }}',{district_id:id},function(data){

				for(var i = 0; i < data.length; i++){
					html += '<option value="'+ data[i].sub_district_id +'">'+data[i].name+'</option>'
				}
				$('.bill_sub_district').html(html)
				
				$('.bill_sub_district').change(function(){
					var sub_id = $(this).val()
					$('.bill_postcode').val(getPostcode(sub_id, data))	
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

		$('.use_billing_address').click(function(){
			if($(this).is(':checked')){
				$('.bill_address').attr('style', 'display: none')
				$('.bill_form').removeAttr('required')
			}else{
				$('.bill_address').show();
				$('.bill_form').attr('required', 'required')
			}
		})
	</script>
	@stop