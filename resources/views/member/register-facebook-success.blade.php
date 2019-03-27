@extends('layouts.main')

@section('content')
    <div class="container margin-bottom">

        <div class="col-lg-12 register-bg register-box">
            <div class="register-area2">
                <div class="thank-you-register-box">
                    <img class="icon-finish" src="{{ asset('assets/images/icon-finish.png') }}">
                </div>

                <div class="text-thank-you-register">
                    <span class="text-success">
                        <b>{{ __('frontend.thank_you_for_register') }}</b>
                    </span>
                    <br>
                    <b>{{ __('frontend.congratulations_you_are_new_member') }}</b>
                </div>

                <div class="box-btn-view">
                    <!--
                                        <a href="#">
                                            <button class="btn-view" type="">View privileges here</button>
                                        </a>
                    -->

                    <a href="{{ route('home.index') }}">
                        <button class="btn-view" type="">{{ __('frontend.go_to_shopping') }}</button>
                    </a>
                </div>
                <!--

                                <div class="box-btn-back-to-cart">
                                    <a href="#">
                                        <button class="btn-back-to-cart" type="">Back to Cart</button>
                                    </a>
                                </div>
                -->

            </div>
        </div>
        <div class="clearfix"></div>

    </div>
@endsection