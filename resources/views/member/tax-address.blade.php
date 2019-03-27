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
                        <b>{{ trans('frontend.tax_address') }}</b>
                    </div>
                    <div class="deta-box2">
                        @include('partials.notifications.alert')
                        <?php if(!empty($taxAddress)){ ?>
                        <div class="col-lg-3 col-md-4 col-sm-3 deta-text no-padding">
                            <b>{{ trans('frontend.company_name') }}/{{ trans('frontend.corporate_name') }}</b>
                        </div>
                        <div class="col-lg-9 col-md-8 col-sm-9 deta-text no-padding">
                            <b>:</b>
                            @if (! empty($makroCardInfo['makro_member_type']) && $makroCardInfo['makro_member_type'] == 'orange')
                                {{ array_get($makroCardInfo, 'request_response.CustName') }}
                            @else
                                {{ array_get($makroCardInfo, 'request_response.CustName', array_get($profile, 'business.shop_name')) }}
                            @endif
                        </div>
                        <div class="clearfix"></div>

                        <div class="col-lg-3 col-md-4 col-sm-3 deta-text no-padding">
                            <b>{{ trans('frontend.headquarters') }}/{{ trans('frontend.branch') }}</b>
                        </div>
                        <div class="col-lg-9 col-md-8 col-sm-9 deta-text no-padding">
                            <b>:</b>
                            @if (! empty($makroCardInfo['makro_member_type']) && $makroCardInfo['makro_member_type'] == 'orange')
                                {{ array_get($makroCardInfo, 'request_response.CustBranch') }}
                            @else
                                {{ array_get($makroCardInfo, 'request_response.CustBranch', array_get($profile, 'business.branch')) }}
                            @endif
                        </div>
                        <div class="clearfix"></div>

                        <div class="col-lg-3 col-md-4 col-sm-3 deta-text no-padding">
                            <b>{{ trans('frontend.tax_id') }}</b>
                        </div>
                        <div class="col-lg-9 col-md-8 col-sm-9 deta-text no-padding">
                            <b>:</b>
                            @if (! empty($makroCardInfo['makro_member_type']) && $makroCardInfo['makro_member_type'] == 'orange')
                                {{ array_get($makroCardInfo, 'request_response.CustTax') }}
                            @else
                                {{ array_get($makroCardInfo, 'request_response.CustTax', array_get($profile, 'tax_id')) }}
                            @endif
                        </div>
                        <div class="clearfix"></div>

                        <div class="col-lg-3 col-md-4 col-sm-3 deta-text no-padding">
                            <b>{{ trans('frontend.tax_address') }}</b>
                        </div>
                        <div class="col-lg-9 col-md-8 col-sm-9 deta-text no-padding">
                            <b>:</b> 
                            {{ array_get($taxAddress, 'address') }} 
                            {{ array_get($taxAddress, 'subdistrict') }} 
                            {{ array_get($taxAddress, 'district') }} 
                            {{ array_get($taxAddress, 'province') }} 
                            {{ array_get($taxAddress, 'postcode') }}
                        </div>
                        <div class="clearfix"></div>

                        {{--<hr class="dashline">

                        <div class="col-lg-3 col-md-4 col-sm-3 deta-text no-padding">
                            <b>{{ trans('frontend.invoice_address') }}</b>
                        </div>
                        <div class="col-lg-9 col-md-8 col-sm-9 deta-text no-padding">
                            <b>:</b> 
                            {{ array_get($billAddress, 'address') }} 
                            {{ array_get($billAddress, 'subdistrict') }} 
                            {{ array_get($billAddress, 'district') }} 
                            {{ array_get($billAddress, 'province') }} 
                            {{ array_get($billAddress, 'postcode') }}
                        </div>
                        <div class="clearfix"></div>--}}

                        <?php }else{ ?>
                            <div class="empty-box text-center">
                                <img src="{{ asset('assets/images/icon-taxinvoice-100px.png') }}" />
                                <h4>{{ trans('frontend.tax_address_is_empty') }}</h4>
                                {{--<a data-toggle="modal" data-target="#editTaxAddressModal" class="btn btn-success">{{ trans('frontend.add_tax_address_data') }}</a>--}}
                            </div>
                        <?php } ?>
                        @include('partials.notifications.error')
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>

@endsection

@section('script-header')

@endsection

@section('script')
    @parent

    <script>
        $(document).ready(function() {
            @if (count($errors) > 0)
                $('#editProfileModal').modal('toggle');
            @endif
        });
    </script>
@stop