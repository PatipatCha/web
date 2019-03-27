@extends('layouts.main')

@section('nav-mobile-menu')

@endsection

@section('content')
<div class="container margin-bottom">
	<div class="bg-w">
		<div class="col-lg-12 contact-us-box no-padding">
			<div class="deta-box5">

				@include('partials.notifications.alert')

				@include('partials.notifications.error')

				<div class="topic-text">
					<b>{{ __('frontend.contact_us') }}</b>
				</div>

				<form method='post' action="{{ route('contact-message') }}" id="contact-us-form" onsubmit="return check_if_capcha_is_filled()">
					<div class="col-lg-6 col-md-6 col-sm-6 padding-right-ad">
						<div class="col-lg-6 col-md-6 col-sm-6 padding-right-ad">
							<label for="">{{ __('frontend.makro_card_id') }}</label>
							<input name="makro_card_id" type="" class="form-control" id="contact_us_makro_card_id"
								   placeholder="{{ __('frontend.makro_card_id') }}" value="{{ old('makro_card_id') }}">
						</div>

						<div class="col-lg-6 col-md-6 col-sm-6 padding-left-ad">
							<label for="">{{ __('frontend.full_name') }}<span style="color:#F01616;"> *</span></label>
							<input name="full_name" type="" class="form-control" id="contact_us_full_name"
								   placeholder="{{ __('frontend.full_name') }}" value="{{ old('full_name') }}">
						</div>

						<div class="col-lg-6 col-md-6 col-sm-6 padding-right-ad">
							<label for="">{{ __('frontend.e_mail') }}<span style="color:#F01616;"> *</span></label>
							<input name="email" type="" class="form-control" id="contact_us_email"
								   placeholder="{{ __('frontend.e_mail') }}" value="{{ old('email') }}">
						</div>

						<div class="col-lg-6 col-md-6 col-sm-6 padding-left-ad">
							<label for="">{{ __('frontend.mobile_phone') }}<span
										style="color:#F01616;"> *</span></label>
							<input name="phone" type="" class="form-control" id="contact_us_phone"
								   placeholder="{{ __('frontend.mobile_phone') }}" value="{{ old('phone') }}">
						</div>

						<div class="col-lg-12 no-padding">
							<label for="">{{ __('frontend.subject') }}<span style="color:#F01616;"> *</span></label>
							<div class="btn-group">
								<select class="form-control" name="subject" data-rule-required="true" required>
									<option value="">-- {{ trans('frontend.contact_us_select_subject') }} --</option>
									@foreach($subjects as $msgSubject)
										<option value="{{ $msgSubject }}"
												@if(old('subject') == $msgSubject) selected="selected" @endif>{{ $msgSubject }}</option>
									@endforeach
								</select>
								<label style="display:none" id="subject-error" class="error" for="subject"></label>
							</div>

						</div>
						<div class="clearfix"></div>


						<div class="col-lg-12 no-padding">
							<label for="">{{ __('frontend.message') }}<span style="color:#F01616;"> *</span></label>
							<textarea name="detail" class="form-control" rows="4">{{ old('detail') }}</textarea>
						</div>
						<div class="clearfix"></div>

						<div class="col-lg-12 no-padding">
							<label for=""></label>
							{!! Anhskohbo\NoCaptcha\Facades\NoCaptcha::display() !!}
							<span style="display: none" id="NoCaptcha_error" class="error">{{ __('frontend.please_verify_you_are_not_robot') }}</span>
						</div>
						<div class="clearfix"></div>

						<div class="btn-box">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
							<button class="btn-continue"
									type="submit">{{ __('frontend.submit_contact_us_message') }}</button>
						</div>

					</div>
				</form>
				<div class="col-lg-6 col-md-6 col-sm-6 padding-left-ad">
					<div class="text-contact-us-box">
						{!! __('frontend.contact_us_head_office_address') !!}
					</div>
					
					<div class="contact-us-map">
						<div class="embed-responsive embed-responsive-16by9">
							<iframe class="embed-responsive-item" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3875.6986707019396!2d100.6250855146703!3d13.736684490357277!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x311d61d164efa365%3A0xf7632167af66680a!2z4Liq4Liz4LiZ4Lix4LiB4LiH4Liy4LiZ4LmD4Lir4LiN4LmI4Liq4Lii4Liy4Lih4LmB4Lih4LmH4LiE4LmC4LiE4Lij!5e0!3m2!1sth!2sth!4v1498622956714"></iframe>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
@endsection

@section('script')
	<script>
        function checkRecaptcha() {
            var res = $('#g-recaptcha-response').val();

            if (res == "" || res == undefined || res.length == 0)
                return false;
            else
                return true;
        }

        function check_if_capcha_is_filled (e) {
            $('#NoCaptcha_error').hide();

            if(checkRecaptcha()) {
                return true;
            }

            $('#NoCaptcha_error').show();
            return false;

        }
	</script>
@endsection