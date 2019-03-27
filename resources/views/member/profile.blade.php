@extends('layouts.main')

@section('nav-mobile-menu')

@endsection

@section('content')
    <div class="container margin-bottom">
        <div class="row member-section">
            @include('member.partials.member-sidebar')

            <div class="col-lg-9 col-md-9 no-padding-left">
                <div class="deta-box3">
                    <div class="topic-text">
                        <b>{{ trans('frontend.my_profile') }}</b>
                        <div class="pull-right">
                            <button class="btn btn-default btn-xs" data-toggle="modal" data-target="#editProfileModal">{{ trans('frontend.edit') }}</button>
                        </div>
                    </div>
                    <div class="deta-box2">
                        <div class="col-lg-3 col-md-4 col-sm-3 deta-text no-padding">
                            <b>{{ trans('frontend.username') }}</b>
                            <a tabindex="0" data-toggle="popover" data-trigger="focus hover" data-placement="bottom" data-content="{{ trans('frontend.login_name') }}">
                                <!-- <img src="{{ asset('assets/images/icon-tips.png') }}" /> -->
                                <i class="fas fa-info-circle"></i>
                            </a>
                        </div>
                        <div class="col-lg-9 col-md-8 col-sm-9 deta-text no-padding">
                            <b>:</b> {{ array_get($profile, 'username') }}
                        </div>
                        <div class="clearfix"></div>

                        <div class="col-lg-3 col-md-4 col-sm-3 deta-text no-padding">
                            <b>{{ trans('frontend.shop_name') }}</b>
                        </div>
                        <div class="col-lg-9 col-md-8 col-sm-9 deta-text no-padding">
                            <b>:</b> {{ array_get($address, 'address_name') }}
                        </div>
                        <div class="clearfix"></div>

                        <div class="col-lg-3 col-md-4 col-sm-3 deta-text no-padding">
                            <b>{{ trans('frontend.fullname') }}</b>
                        </div>
                        <div class="col-lg-9 col-md-8 col-sm-9 deta-text no-padding">
                            <b>:</b> {{ array_get($profile, 'first_name') }} {{ array_get($profile, 'last_name') }}
                        </div>
                        <div class="clearfix"></div>

                        <div class="col-lg-3 col-md-4 col-sm-3 deta-text no-padding">
                            <b>{{ trans('frontend.address') }}</b>
                        </div>
                        <div class="col-lg-9 col-md-8 col-sm-9 deta-text no-padding">
                            <b>:</b> 
                            {{ array_get($address, 'address') }} 
                            {{ array_get($address, 'subdistrict') }} 
                            {{ array_get($address, 'district') }} 
                            {{ array_get($address, 'province') }} 
                            {{ array_get($address, 'postcode') }}
                        </div>
                        <div class="clearfix"></div>

                        <div class="col-lg-3 col-md-4 col-sm-3 deta-text no-padding">
                            <b>{{ trans('frontend.birth_date') }}</b>
                        </div>
                        <div class="col-lg-9 col-md-8 col-sm-9 deta-text no-padding">
                            <b>:</b> {{ array_get($profile, 'birth_day') }}
                        </div>
                        <div class="clearfix"></div>

                        <div class="col-lg-3 col-md-4 col-sm-3 deta-text no-padding">
                            <b>{{ trans('frontend.mobile_phone') }}</b>
                        </div>
                        <div class="col-lg-9 col-md-8 col-sm-9 deta-text no-padding">
                            <b>:</b> {{ array_get($profile, 'phone') }}
                        </div>
                        <div class="clearfix"></div>

                        <div class="col-lg-3 col-md-4 col-sm-3 deta-text no-padding">
                            <b>{{ trans('frontend.email') }}</b>
                        </div>
                        <div class="col-lg-9 col-md-8 col-sm-9 deta-text no-padding">
                            <b>:</b> {{ array_get($profile, 'email') }}
                        </div>
                        <div class="clearfix"></div>    

                        <div class="col-lg-3 col-md-4 col-sm-3 deta-text no-padding">
                            <b>{{ trans('frontend.makro_card_id') }}</b>
                        </div>
                        <div class="col-lg-9 col-md-8 col-sm-9 deta-text no-padding">
                            <b>:</b> {{ array_get($profile, 'makro_card_id') }}
                        </div>
                        <div class="clearfix"></div>

                        @include('partials.notifications.alert')
                        @include('partials.notifications.error')
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>

    @widget('App\Widgets\Products\Recent', ['show' => 6])

    @include('member.partials.profile-modal')
@endsection

@section('script-header')

@endsection

