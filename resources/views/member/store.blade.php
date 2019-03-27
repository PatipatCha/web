@extends('layouts.main')

@section('nav-mobile-menu')

@endsection

@section('content')
    <div class="container margin-bottom">
        <div class="row member-section">
            @include('member.partials.member-sidebar')

            <div class="col-lg-9 col-md-9 no-padding">

                <div class="deta-box">
                    <div class="topic-text">
                        <b>{{ trans('frontend.store_profile') }}</b>
                        <div class="pull-right">
                            <button class="btn-edit" data-toggle="modal" data-target="#editProfileModal">{{ trans('frontend.edit') }}</button>
                        </div>
                    </div>

                    <div class="deta-box2">
                        <div class="clearfix"></div>
                        <div class="col-lg-3 col-md-4 col-sm-3 deta-text no-padding">
                            <b>{{ trans('frontend.shop_name') }}</b>
                        </div>
                        <div class="col-lg-9 col-md-8 col-sm-9 deta-text no-padding">
                            <b>:</b> {{ array_get($profile, 'shop_name') }}
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-lg-3 col-md-4 col-sm-3 deta-text no-padding">
                            <b>{{ trans('frontend.first_name') }}/{{ trans('frontend.last_name') }}</b>
                        </div>
                        <div class="col-lg-9 col-md-8 col-sm-9 deta-text no-padding">
                            <b>:</b> {{ array_get($profile, 'first_name') }} {{ array_get($profile, 'last_name') }}
                        </div>
                        <div class="clearfix"></div>

                        <div class="col-lg-3 col-md-4 col-sm-3 deta-text no-padding">
                            <b>{{ trans('frontend.phone') }}</b>
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
                            <b>Address</b>
                        </div>
                        <div class="col-lg-9 col-md-8 col-sm-9 deta-text no-padding">
                            <b>:</b> 76/1 moo. 13 soi. Tachan
                        </div>
                        <div class="clearfix"></div>

                        <div class="col-lg-3 col-md-4 col-sm-3 deta-text no-padding">
                            <b>Road/Street</b>
                        </div>
                        <div class="col-lg-9 col-md-8 col-sm-9 deta-text no-padding">
                            <b>:</b> Suk Sawat
                        </div>
                        <div class="clearfix"></div>

                        <div class="col-lg-3 col-md-4 col-sm-3 deta-text no-padding">
                            <b>Township</b>
                        </div>
                        <div class="col-lg-9 col-md-8 col-sm-9 deta-text no-padding">
                            <b>:</b> Nai Khlong Bang Pla Kotp
                        </div>
                        <div class="clearfix"></div>

                        <div class="col-lg-3 col-md-4 col-sm-3 deta-text no-padding">
                            <b>City</b>
                        </div>
                        <div class="col-lg-9 col-md-8 col-sm-9 deta-text no-padding">
                            <b>:</b> Phra Samut Chedi
                        </div>
                        <div class="clearfix"></div>

                        <div class="col-lg-3 col-md-4 col-sm-3 deta-text no-padding">
                            <b>Province</b>
                        </div>
                        <div class="col-lg-9 col-md-8 col-sm-9 deta-text no-padding">
                            <b>:</b> Samut Prakan
                        </div>
                        <div class="clearfix"></div>

                        <div class="col-lg-3 col-md-4 col-sm-3 deta-text no-padding">
                            <b>PostCode/Zipcode</b>
                        </div>
                        <div class="col-lg-9 col-md-8 col-sm-9 deta-text no-padding">
                            <b>:</b> 10290
                        </div>
                        <div class="clearfix"></div>

                        @include('partials.notifications.alert')
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>

    @include('member.partials.profile-modal')
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