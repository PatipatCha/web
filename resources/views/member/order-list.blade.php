@extends('layouts.main')

@section('nav-mobile-menu')

@endsection

@section('content')
    <?php  $thaiDate = new \App\Bootstrap\Helpers\ThaiDate(); ?>
    <div class="container margin-bottom">
        <div class="row member-section">
            @include('member.partials.member-sidebar')

            <div class="col-lg-9 col-md-9 no-padding">
                {{--<div class="bg-profile profile-photo">
                    <div class="proflie-photo-box">
                        <img src="images/profile-photo.png" alt="" class="img-circle">
                    </div>

                    <div class="profile-name-text">
                        <b>Krittinon Homklin</b>
                    </div>
                    <div class="profile-point-text">
                        <img class="icon-point" src="images/icon-point.png">
                        <b>My Points <span style="color:#F01616; font-size:16px;">1,000</span></b>
                    </div>
                </div>--}}
                <div class="deta-box3">
                    <div class="topic-text title">
                        <b>{{ trans('frontend.my_order') }}</b>
                    </div>
                    @if($orderLists->isEmpty())
                        <div class="empty-box text-center">
                            <img src="{{ asset('assets/images/icon-order-100px.png') }}"/>
                            <h4>{{ trans('frontend.order_is_empty') }}</h4>
                            <a href="{{ route('home.index') }}"
                               class="btn btn-primary">{{ trans('frontend.go_to_shopping') }}</a>
                        </div>
                    @else
                        <div class="order-history">
                            <div class="row deta-box2">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <select name="sort_by" id="" class="form-control sort_order">
                                            <option value="">-- {{ trans('frontend.select_all') }} --</option>
                                            <option {{ request()->get('sort_by') == 'limit'? 'selected':'' }} value="limit">{{ trans('frontend.last_order') }}</option>
                                            <option {{ request()->get('sort_by') == 'one_month'? 'selected':'' }} value="one_month">{{ trans('frontend.last_day') }}</option>
                                            <option {{ request()->get('sort_by') == 'six_month'? 'selected':'' }} value="six_month">{{ trans('frontend.last_month') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            @foreach($orderLists as $order)
                                <div class="product-item clearfix">
                                    <div class="pd-category topic-table4">
                                        <div class="topic-table1">
                                            <div class="col-sm-3 box">
                                                <div>{{ trans('frontend.order_no') }}</div>
                                                <div class="text-bold">
                                                    <span class="order-number">{{ array_get($order, 'order_no') }}</span>
                                                    <!-- <a href="{{ route('members.order-detail',['order_id' => array_get($order, 'order_no')]) }}"
                                                        class="info">{{ __('frontend.view_order_detail') }}</a> -->
                                                </div>
                                            </div>
                                            
                                            <div class="col-sm-3 box">
                                                <div class="order-date">
                                                    <div>{{ trans('frontend.order_date') }}</div>
                                                    <div class="text-bold">
                                                        <span class="date">
                                                            @if (app()->isLocale('th'))
                                                                <?php
                                                                $thaiDate = new App\Bootstrap\Helpers\ThaiDate;
                                                                $thaiDate->buddhist_era = true;
                                                                echo $thaiDate->date('d M Y',
                                                                    strtotime(array_get($order,
                                                                        'order_date')));
                                                                ?>
                                                            @else
                                                                {{ Carbon\Carbon::parse(array_get($order, 'order_date'))->format('d M Y') }}
                                                            @endif
                                                        </span>
                                                        <span class="time">
                                                            {{ trans('frontend.time') }}
                                                            @if (app()->isLocale('th'))
                                                                <?php
                                                                $thaiDate = new App\Bootstrap\Helpers\ThaiDate;
                                                                $thaiDate->buddhist_era = false;
                                                                echo $thaiDate->date('H:i',
                                                                    strtotime(array_get($order,
                                                                        'order_date')));
                                                                ?>
                                                            @else
                                                                {{ Carbon\Carbon::parse(array_get($order, 'order_date'))->format('H:i') }}
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-3 box">
                                                @if(strtolower(array_get($order, 'payment.payment_gateway_code')) == 'payatstore')
                                                    <div>{{ trans('frontend.payment_channel') }}</div>
                                                    <div class="text-bold">{{ trans('frontend.pay_at') }}
                                                        <span class="store-branch">{{ array_get($order, 'store.name') }}</span>
                                                    </div>
                                                @else
                                                    <div>{{ trans('frontend.payment_channel') }}</div>
                                                    <div class="text-bold">{{ array_get($order, 'payment.title') }}</div>
                                                @endif
                                            </div>

                                            <div class="col-sm-3 box">
                                                <div class="order-status {{ snake_case(array_get($order, 'order_status')) }}">
                                                    <div class="icon"></div>
                                                    <div class="order-status-action">
                                                        <div class="title">{{ trans('frontend.order_status') }}</div>
                                                        <div class="value text-bold">{{ trans('frontend.order_status_'.snake_case(array_get($order, 'order_status'))) }}</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-12 shipping box">
                                                @if(array_get($order, 'delivery_type') == 'pickup')
                                                    {{ trans('frontend.order_pickup_receive_at', ['name' => '']) }} <b class="text-bold">{{ array_get($order, 'store.name') }}</b>
                                                @else
                                                    {{ trans('frontend.order_shipping_at') }} <b class="text-bold">{{ \App\Bootstrap\Helpers\CartHelper::getFullAddress(array_get($order, 'shipping_address')) }}</b>
                                                @endif
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>

                                    <?php
                                    $products = collect(array_get($order, 'items'))->map(function ($item) {
                                        return [
                                            'content_id' => $item['item_id'],
                                            'quantity' => $item['quantity']
                                        ];
                                    });
                                    $numToShow = 2;
                                    $numAll = sizeof($order['items']);
                                    $order['items'] = array_slice($order['items'], 0, $numToShow);

                                    ?>

                                    @foreach($order['items'] as $product)
                                        <div class="cart-pd-box row">
                                            <div class="col-xs-12 col-sm-8 col-md-9 no-padding">
                                                <div class="pd-img">
                                                    @if (! empty(array_get($product, 'image')))
                                                        <img src="{{ array_get($product, 'image') }}" class="img-responsive">
                                                    @endif
                                                </div>
                                                <div class="col-xs-7 col-sm-8 col-md-9 no-padding">
                                                    <div class="margin-bottom-5">
                                                        <div class="cart-pd-detail-box cart-text-detail">
                                                            <b class="text-bold">{{ array_get($product, 'original_name.' . \App::getLocale()) }}</b><br>
                                                            <!--<b>Code {{ array_get($product, 'name') }}</b><br>-->
                                                            <!--#Assortment<br>-->
                                                            <b>{{ __('frontend.product_code') }}</b> {{ array_get($product, 'id') }}
                                                            <br/>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="cart-pd-detail-box hidden-md hidden-lg">
                                                            <b>{{ trans('frontend.quantities') }} {{ number_format(array_get($product, 'quantity')) }} {{ \App::isLocale('th') ? trans('frontend.piece') : str_plural(trans('frontend.piece_singular'), array_get($product, 'quantity')) }}</b>
                                                        </div>
                                                        <div class="cart-pd-detail-box hidden-md hidden-lg">
                                                            <b>{{ trans('frontend.subtotal') }} {{ number_format(array_get($product, 'price')*array_get($product, 'quantity'), 2, '.', ',') }}
                                                                ฿</b>
                                                        </div>
                                                        <div class="col-sm-5 no-padding">
                                                            <div class="cart-pd-detail-box">
                                                                <span class="item-price">{{ number_format(array_get($product, 'price'), 2, '.', ',') }} ฿</span>
                                                                @if(array_get($product, 'price') < array_get($product, 'normal_price'))
                                                                    <s>{{ number_format(array_get($product, 'normal_price'), 2, '.', ',') }}
                                                                        ฿</s>
                                                                @endif
                                                                <br>
                                                                {{ trans('frontend.per') }} {{ array_get($product, 'price_per_key') }}
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-offset-3 hidden-xs hidden-sm">
                                                            <div class="cart-pd-detail-box">
                                                                <b>{{ trans('frontend.quantities') }} {{ number_format(array_get($product, 'quantity')) }} {{ \App::isLocale('th') ? trans('frontend.piece') : str_plural(trans('frontend.piece_singular'), array_get($product, 'quantity')) }}</b>
                                                            </div>
                                                            <div class="cart-pd-detail-box">
                                                                <b>{{ trans('frontend.subtotal') }} {{ number_format(array_get($product, 'price')*array_get($product, 'quantity'), 2, '.', ',') }}
                                                                    ฿</b>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php
                                                $show_status = true;

                                                if (in_array($order['order_status'], ['Payment Failed', 'Order Expired', 'Order Created'])) {
                                                    $show_status = false;
                                                }

                                                $payment_gateway = strtolower(array_get($order, 'payment.payment_gateway'));

                                                if ($payment_gateway == '123service' && $order['order_status'] == 'Waiting For Payment') {
                                                    $show_status = false;
                                                }
                                            ?>
                                            
                                            @if($show_status)
                                                <div class="col-xs-12 col-sm-4 col-sm-offset-0 col-md-3 col-md-offset-0 no-padding">
                                                    @if(! empty($product['item_status']))
                                                        <div class="cart-pd-detail-box">
                                                            <div class="waiting">
                                                                <div class="media">
                                                                    <?php
                                                                    $refund_qty = array_get($product, 'refund.summary.quantity');
                                                                    $product_qty = array_get($product, 'quantity');
                                                                    ?>

                                                                    @if($refund_qty >= $product_qty)
                                                                        <div class="media-left">
                                                                            <img src="/images/item_status_canceled.png"
                                                                                 class="media-object"/>
                                                                        </div>
                                                                        <div class="media-body">

                                                                            <b>
                                                                                {{ trans('frontend.order_item_status_shipping_cancel_order') }} <!-- ใช้ key นี้ เพราะแปลเหมือนกันทั้ง shipping และ pickup -->
                                                                            </b>
                                                                        </div>
                                                                    @else
                                                                        <div class="media-left">
                                                                            <img src="{{ array_get($product, 'item_status_icon') }}"
                                                                                 class="media-object"/>
                                                                        </div>
                                                                        <div class="media-body">

                                                                            <b>
                                                                                {!! array_get($product, 'item_status_text') !!}
                                                                            </b>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="clearfix"></div>
                                        </div>

                                    @endforeach
                                    <div class="all-product">
                                        <div class="order-price">
                                            <div>
                                                {{ trans('frontend.order_list_grand_total') }}
                                            </div>
                                            <div class="text-bold">
                                                <span class="price">{{ number_format(array_get($order, 'summary.grand_total'), 2, '.', ',') }} ฿</span>
                                            </div>
                                        </div>
                                        <div class="re-order">
                                            <re-order-history :products="{{ json_encode($products) }}" name="{{ trans('frontend.repeat_your_order') }}"></re-order-history>
                                        </div>

                                        <a href="{{ route('members.order-detail',['order_id' => array_get($order, 'order_no')]) }}" class="btn btn-default">{{__('frontend.view_order_detail')}}</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="pagination col-sm-12 pull-right no-padding hidden-xs">
                            {!! $orderLists->appends(request()->query())->render() !!}
                        </div>

                        <div class="pagination col-sm-12 col-xs-12 no-padding visible-xs">
                            {!! $orderLists->appends(request()->query())->render() !!}
                        </div>
                    @endif
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    {{--<confirm-remove-wish-list></confirm-remove-wish-list>--}}
@endsection

@section('script-header')

@endsection

@section('script')
    @parent

@stop
