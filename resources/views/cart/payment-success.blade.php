@extends('layouts.main')

@section('nav-mobile-menu')

@endsection

@section('content')
    @include('cart.step', ['current' => 4])


    <!--================================================== -->
    <div class="container margin-bottom order-history-detail">
        <div class="col-lg-12 cart-payment-bg pay-thank-box">
            @if (strtolower($orderDetail['detail']['payment']['payment_type']) == 'offline')
                <div class="thank-box">
                    <img class="icon-finish-thank" src="{{ asset('assets/images/icon-time2.png') }}">
                    <div class="text-thank">
                        <b><span style="color:#7ED321;">{!! trans('frontend.waiting_for_payment') !!}</span><br></b>

                        @if (strtolower($orderDetail['detail']['payment']['payment_gateway_code']) == 'payatstore')
                            <p>
                                <?php
                                $expireDate = Carbon\Carbon::parse(array_get($orderDetail, 'detail.payment.expire'));

                                if (app()->isLocale('th')) {
                                    $thaiDate = new App\Bootstrap\Helpers\ThaiDate;
                                    $thaiDate->buddhist_era = true;
                                    $paymentExpireDate = $thaiDate->date('j F Y g:ia', $expireDate->getTimestamp());
                                } else {
                                    $paymentExpireDate = $expireDate->format('วันที่ j F Y เวลา H:i');
                                }
                                ?>

                                {!! trans('frontend.pay_at_store_waiting_for_payment_message', ['payment_expire_at' => $paymentExpireDate, 'store_name' => $detail['store']['name']]) !!}
                            </p>
                        @endif
                    </div>
                </div>
            @else
                @if (snake_case(array_get($orderDetail, 'detail.order_status')) == 'waiting_for_payment')
                    <div class="thank-box">
                        <img class="icon-finish-thank" src="{{ asset('assets/images/icon-time2.png') }}">
                        <div class="text-thank">
                            <b><span style="color:#7ED321;">{!! trans('frontend.waiting_for_payment') !!}</span><br></b>

                            <p>
                                {!! trans('frontend.pay_by_gateway_waiting_message', ['hours' => array_get($orderDetail, 'detail.payment.expire_in_hour')]) !!}
                            </p>

                            <div class="warning">
                                <?php
                                $expireDate = Carbon\Carbon::parse(array_get($orderDetail, 'detail.payment.expire'));

                                if (app()->isLocale('th')) {
                                    $thaiDate = new App\Bootstrap\Helpers\ThaiDate;
                                    $thaiDate->buddhist_era = true;
                                    $paymentExpireDate = $thaiDate->date('วันที่ j F Y เวลา H:i',
                                        $expireDate->getTimestamp());
                                } else {
                                    $paymentExpireDate = $expireDate->format('j F Y g:ia');
                                }
                                ?>

                                {!! trans('frontend.pay_by_gateway_waiting_expire_datetime_message', ['expire_datetime' => $paymentExpireDate, 'payment_channel' => $orderDetail['detail']['payment']['title']]) !!}
                            </div>
                        </div>
                    </div>
                @elseif (in_array(snake_case(array_get($orderDetail, 'detail.order_status')), ['order_pending', 'order_created', 'order_confirmed', 'in_progress']))
                    <div class="thank-box">
                        <img class="icon-finish-thank" src="{{ asset('assets/images/icon-finish.png') }}">
                        <div class="row">
                            <div class="text-thank col-sm-8 col-sm-offset-2">
                                {!! trans('frontend.your_order_has_been_processed_successfully', ['orderNo' => $detail['order_no']]) !!}
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <div class="thank-box-print">
                <div class="item-box">
                    <div class="topic-text title">
                        <b>{{ trans('frontend.order_detail') }}</b>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6 print-col">
                        <div class="row">
                            <div class="item-box-print clearfix">
                                <address>
                                    <div class="item-box clearfix">
                                        <div class="item">
                                            <div class="col-sm-4 pull-left">
                                                <b>{{ trans('frontend.order_no') }}</b>
                                            </div>
                                            <div class="col-sm-8">
                                                <b class="text-bold">{{ $detail['order_no'] }}</b>
                                            </div>
                                        </div>
                                        <div class="item">
                                            <div class="col-sm-4 pull-left">
                                                <b>{{ trans('frontend.order_date') }}</b>
                                            </div>
                                            <div class="col-sm-8">
                                                <b class="text-bold">
                                                    @if (app()->isLocale('th'))
                                                        <?php
                                                        $thaiDate = new App\Bootstrap\Helpers\ThaiDate;
                                                        $thaiDate->buddhist_era = true;
                                                        echo $thaiDate->date('d M Y',
                                                            strtotime(array_get($detail, 'order_date')));
                                                        ?>
                                                    @else
                                                        {{ Carbon\Carbon::parse(array_get($detail, 'order_date'))->format('d M Y') }}
                                                    @endif
                                                </b>
                                            </div>
                                        </div>
                                        <div class="item clearfix">
                                            <div class="col-sm-4 pull-left">
                                                <b>{{ trans('frontend.order_status') }}</b>
                                            </div>
                                            <div class="col-sm-8">
                                                <b class="text-bold">{{ trans('frontend.order_status_'.snake_case(array_get($orderDetail, 'detail.order_status'))) }}</b>
                                            </div>
                                        </div>
                                        <div class="item clearfix">
                                            <div class="col-sm-4 pull-left">
                                                <b>{{ trans('frontend.order_receive_type') }}</b>
                                            </div>
                                            <div class="col-sm-8">
                                                <b class="text-bold">{{ $detail['pickup_type_names'] }}</b>
                                            </div>
                                        </div>
                                        <div class="item clearfix">
                                            <div class="col-sm-4 pull-left">
                                                <b>{{ trans('frontend.payment_method') }}</b>
                                            </div>
                                            <div class="col-sm-8">
                                                <b class="text-bold">
                                                    @if (strtolower(array_get($orderDetail, 'detail.payment.payment_gateway_code')) == 'payatstore')
                                                        {!! trans('frontend.pay_at_store_name', ['store_name' => array_get($orderDetail, 'detail.store.name')]) !!}
                                                    @else
                                                        {{ array_get($orderDetail, 'detail.payment.title') }}
                                                    @endif
                                                </b>
                                            </div>
                                        </div>
                                    </div>
                                </address>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 print-col">
                        <div class="scan-barcode">
                            <img src="{{ $detail['barcode'] }}" class="img-responsive">
                            <div class="id">
                                {{ trans('frontend.order_no') }}: {{ $detail['order_no'] }}
                            </div>
                        </div>
                    <!-- <div class="col-sm-12 hidden-print">
                            <a href="javascript: window.print()" class="btn btn-default btn-block" id="bnt_print">
                                <i class="fa fa-print"></i> {{ trans('frontend.print') }}
                            </a>
                        </div> -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 print-col">
                        <div class="item-box">
                            <div class="topic-text"><b
                                        class="text-bold">{{ trans('frontend.order_customer_detail') }}</b></div>
                            <div class="row">
                                <address>
                                    <div class="item clearfix">
                                        <div class="col-sm-4 pull-left">
                                            <b>{{ trans('frontend.shop_name') }}</b>
                                        </div>
                                        <div class="col-sm-8">
                                            <b class="text-bold">{{ $detail['buyer']['address']['content_name'] }}</b>
                                        </div>
                                    </div>
                                    <div class="item clearfix">
                                        <div class="col-sm-4 pull-left">
                                            <b>{{ trans('frontend.buyer_name') }}</b>
                                        </div>
                                        <div class="col-sm-8">
                                            <b class="text-bold">{{ $detail['buyer']['address']['first_name'] }} {{ $detail['buyer']['address']['last_name'] }}</b>
                                        </div>
                                    </div>
                                    <div class="item clearfix">
                                        <div class="col-sm-4 pull-left">
                                            <b>{{ trans('frontend.telephone_number') }}</b>
                                        </div>
                                        <div class="col-sm-8">
                                            <b class="text-bold">{{ $detail['buyer']['address']['contact_phone'] }}</b>
                                        </div>
                                    </div>
                                    <div class="item clearfix">
                                        <div class="col-sm-4 pull-left">
                                            <b>{{ trans('frontend.email') }}</b>
                                        </div>
                                        <div class="col-sm-8">
                                            <b class="text-bold">{{ $detail['buyer']['address']['contact_email'] }}</b>
                                        </div>
                                    </div>
                                    <div class="item clearfix">
                                        <div class="col-sm-4 pull-left">
                                            <b>{{ trans('frontend.makro_card_id') }}</b>
                                        </div>
                                        <div class="col-sm-8">
                                            <b class="text-bold">{{ empty(array_get($detail, 'buyer.makro_card_id'))?'-':array_get($detail, 'buyer.makro_card_id') }}</b>
                                        </div>
                                    </div>
                                </address>
                            </div>
                        </div>
                    </div>

                    @if (!empty($detail['tax_address']))
                        <div class="col-sm-6 print-col">
                            <div class="item-box clearfix">
                                <div class="topic-text">
                                    <b class="text-bold">{{ trans('frontend.tax_invoice') }}{{--ใบกำกับภาษี--}}</b>
                                </div>
                                <address>
                                    <div class="row">
                                        <div class="col-sm-4 pull-left">{{ trans('frontend.company') }}</div>
                                        <div class="col-sm-8">
                                            <b class="text-bold">{{ $detail['buyer']['information']['business']['shop_name'] }}</b>
                                        </div>
                                        <div class="clearfix"></div>

                                        <div class="col-sm-4 pull-left">{{ trans('frontend.branch_no') }}</div>
                                        <div class="col-sm-8">
                                            <b class="text-bold">{{ $detail['buyer']['information']['business']['branch'] }}</b>
                                        </div>
                                        <div class="clearfix"></div>

                                        <div class="col-sm-4 pull-left">{{ trans('frontend.tax_id') }}</div>
                                        <div class="col-sm-8">
                                            <b class="text-bold">{{ $detail['buyer']['information']['tax_id'] }}</b>
                                        </div>
                                        <div class="clearfix"></div>

                                        <div class="col-sm-4 pull-left">{{ trans('frontend.address') }}</div>
                                        <div class="col-sm-8">
                                            <b class="text-bold">{{ \App\Bootstrap\Helpers\CartHelper::getFullAddress($detail['tax_address']) }}</b>
                                        </div>
                                        <div class="clearfix"></div>

                                    <!-- <div class="col-sm-4">{{ trans('frontend.tel') }}</div>
                                    <div class="col-sm-8">
                                        <b class="text-bold">{{ $detail['tax_address']['mobile_phone'] }}</b>
                                    </div>

                                    <div class="col-sm-4">{{ trans('frontend.email') }}</div>
                                    <div class="col-sm-8">
                                        <b class="text-bold">{{ $detail['tax_address']['email_id'] }}</b>
                                    </div> -->
                                    </div>
                                </address>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="row">
                    <div class="item-box clearfix">
                        @if($orderDetail['detail']['delivery_type'] == 'shipping' || !$orderDetail['detail']['delivery_type'])
                            @if (!empty($detail['shipping_address']))
                                <div class="col-sm-12">
                                    <div class="item-box clearfix">
                                        <div class="topic-text">
                                            <b>{{ trans('frontend.order_shipping_address') }}</b>
                                        </div>

                                        <address>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    @if(! empty($detail['shipping_address']['shop_name']))
                                                        <div class="shop_name">
                                                            <b class="text-bold">{{ $detail['shipping_address']['shop_name'] }}</b>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="col-sm-12">
                                                    <div class="shop_buyer_name">
                                                        {{ $detail['shipping_address']['first_name'] }}
                                                        {{ $detail['shipping_address']['last_name'] }}
                                                    </div>
                                                </div>

                                                <div class="col-sm-2 pull-left">{{ trans('frontend.address') }}</div>
                                                <div class="col-sm-10">
                                                    <b class="text-bold">{{ \App\Bootstrap\Helpers\CartHelper::getFullAddress($detail['shipping_address']) }}</b>
                                                </div>

                                                <div class="col-sm-2 pull-left">{{ trans('frontend.telephone_number') }}</div>
                                                <div class="col-sm-10">
                                                    <b class="text-bold">{{ $detail['shipping_address']['mobile_phone'] }}</b>
                                                </div>

                                                <div class="col-sm-2 pull-left">{{ trans('frontend.email') }}</div>
                                                <div class="col-sm-10">
                                                    <b class="text-bold">{{ $detail['shipping_address']['email_id'] }}</b>
                                                </div>
                                            </div>
                                        </address>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="col-sm-12 print-col">
                                <div class="item-box clearfix">
                                    <div class="topic-text">
                                        <b>{{ trans('frontend.order_pickup_receive_at', ['name' => array_get($orderDetail, 'detail.store.name')]) }}</b>
                                    </div>

                                    <address>
                                        <div class="row">
                                            <div class="col-sm-2">{{ trans('frontend.order_pickup_makro_store') }}</div>
                                            <div class="col-sm-10">
                                                <b class="text-bold">{{ array_get($orderDetail, 'detail.store.name') }}</b>
                                            </div>
                                            <div class="col-sm-2">{{ trans('frontend.order_pickup_makro_store_address') }}</div>
                                            <div class="col-sm-10">
                                                <b class="text-bold">
                                                    {{ array_get($orderDetail, 'detail.store.address.address') }}
                                                    {{ array_get($orderDetail, 'detail.store.address.subdistrict') }}
                                                    {{ array_get($orderDetail, 'detail.store.address.district') }}
                                                    {{ array_get($orderDetail, 'detail.store.address.province') }}
                                                    {{ array_get($orderDetail, 'detail.store.address.postcode') }}
                                                </b>
                                                <view-map-button
                                                        :store-data="{{ json_encode(array_get($orderDetail, 'detail.store')) }}"></view-map-button>
                                            </div>

                                            <div class="col-sm-2">{{ trans('frontend.contact_number') }}</div>
                                            <div class="col-sm-10">
                                                <b class="text-bold">{{ trans('frontend.store_mobile') }}</b>
                                            </div>
                                        </div>
                                    </address>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <?php

            $show_status = true;

            if (in_array($orderDetail['detail']['order_status'],
                ['Payment Failed', 'Order Expired', 'Order Created'])) {
                $show_status = false;
            }

            $payment_gateway = strtolower(array_get($orderDetail['detail'], 'payment.payment_gateway'));

            if ($payment_gateway == '123service' && $orderDetail['detail']['order_status'] == 'Waiting For Payment') {
                $show_status = false;
            }
            ?>
            <div class="result margin-bottom-10">
                <div class="row">
                    <div class="col-sm-12">

                        <div class="print hidden-print">
                            <span>
                                <a class="btn btn-primary hidden-print" href="{{ route('home.index') }}"
                                   id="btn_go_to_shopping">
                                    <i class="far fa-arrow-right"></i> {{ __('frontend.continue_shopping_again') }}
                                </a>
                            </span>
                            <a href="javascript:window.print()" class="btn btn-default">
                                <i class="far fa-print"></i> {{ __('frontend.print_order') }}
                            </a>
                        </div>
                        <h3 class="text-title text-bold">{{ __('frontend.your_order') }}</h3>
                    </div>
                </div>

                <cart-success
                        v-bind:cart="{{ htmlspecialchars(json_encode($cart)) }}"
                        v-bind:cart-summary="{{ json_encode($summary) }}"
                        v-bind:promotions="{{ json_encode($promotions) }}"
                        v-bind:clear-cart="1"
                        :estimated-dates="{{ json_encode($delivery_dates) }}"
                        :show-status="'{{ $show_status }}'"
                        :calculate-date="true"
                        :delivery-type="'{{ $orderDetail['detail']['delivery_type']? $orderDetail['detail']['delivery_type']: 'shipping' }}'"
                        :order-date="'{{ $orderDetail['detail']['order_date'] }}'"
                        :config-delivery-date="{{ json_encode(array_get($orderDetail, 'detail.config_delivery_date')) }}"

                >
                </cart-success>
            </div>

            <div class="print hidden-print">
                <span>
                    <a class="btn btn-primary hidden-print" href="{{ route('home.index') }}" id="btn_go_to_shopping">
                        <i class="far fa-arrow-right"></i> {{ __('frontend.continue_shopping_again') }}
                    </a>
                </span>
                <a href="javascript:window.print()" class="btn btn-default">
                    <i class="far fa-print"></i> {{ __('frontend.print_order') }}
                </a>
            </div>

            <div class="fake-div"></div>
        </div>
    </div>
    <!--================================================== -->
@endsection

@section('script')
    <script>
        $(function () {
            var transaction = {
                'id': '{{ array_get($orderDetail, 'ms_order_no') }}',
                'store_id': '{{ array_get($orderDetail, 'detail.store.id') }}',
                'store_area': '{{ array_get($orderDetail, 'detail.store.name') }}',
                'payment_type': '{{ array_get($orderDetail, 'detail.payment.payment_gateway_code') }}',
                'shipping_type': '{{ array_get($orderDetail, 'detail.delivery_type') }}',
                'cost': '{{ array_get($summary, 'total_cost') }}',
                'makro_member_id': '{{ array_get($orderDetail, 'detail.buyer.makro_card_id') }}',
                'makro_customer_group_id': '', //TODO : makro_customer_group_id ???
                'revenue': '{{ array_get($summary, 'delivery_fee') }}', // ในหน้า Sales Performance ค่า revenue จะถูกนำไปรวม กับราคาสินค้าทั้งหมด ทำให้ราคาที่แสดงไม่ถูกต้อง จึงจำเป็นต้องส่งค่าขนส่งไปแทน
                'tax': '{{ $vat_total }}',
                'shipping': '{{ array_get($summary, 'delivery_fee') }}',
                'buyer_id': '{{ array_get($orderDetail, 'detail.buyer.online_customer_id') }}',
                'buyer_name': '{{ array_get($orderDetail, 'detail.buyer.first_name') }} {{ array_get($orderDetail, 'detail.buyer.last_name') }}',
            }

            gaPurchase(transaction, '{!! addslashes(json_encode(array_get($orderDetail, 'detail.items'))) !!}')
        });

    </script>
@endsection