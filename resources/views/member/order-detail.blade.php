@extends('layouts.main')

@section('nav-mobile-menu')

@endsection

@section('content')
<div class="container margin-bottom">
	<div class="bg-w">
		{{-- @include('member.partials.member-sidebar') --}}

		<div class="clearfix"></div>

		<div class="order-history-detail">
			<div class="deta-box3 clearfix">
				<div class="topic-text title">
					<b>{{ trans('frontend.order_detail') }}</b>
					<?php
							$products = collect(array_get($orders, 'detail.items'))->map(function($item) {
							  return [
									'content_id' => $item['item_id'],
									'quantity' => $item['quantity']
								];
							});

						?>

					{{--<re-order-wish-list>--}}
						{{--</re-order-wish-list>--}}

					<div class="print hidden-print">
						<re-order-history :products="{{ json_encode($products->toArray()) }}" name="{{ trans('frontend.repeat_your_order')  }}"></re-order-history>
						<a href="javascript:window.print()" class="btn btn-default">
							<i class="far fa-print"></i> {{ __('frontend.print_order') }}
						</a>
					</div>
				</div>
				<div class="item-box">
					<div class="row">
						<div class="col-sm-6 print-col">
							<address>
								<div class="row">
									<div class="item">
										<div class="col-sm-4 pull-left">
											<b>{{ trans('frontend.order_no') }}</b>
										</div>
										<div class="col-sm-8">
											<b class="text-bold">{{ array_get($orders, 'detail.order_no') }}</b>
										</div>
									</div>
									<div class="item">
										<div class="col-sm-4 pull-left">
											<b>{{ trans('frontend.order_date') }}</b>
										</div>
										<div class="col-sm-8">
											@if (app()->isLocale('th'))
											<b class="text-bold">
												<?php
															$thaiDate = new App\Bootstrap\Helpers\ThaiDate;
															$thaiDate->buddhist_era = true;
															echo $thaiDate->date('d M Y H:i', strtotime(array_get($orders, 'detail.order_date')));
														?>
											</b>
											@else
											<b class="text-bold">
												{{ Carbon\Carbon::parse(array_get($orders, 'detail.order_date'))->format('d M Y H:i') }}
											</b>
											@endif
										</div>
									</div>
									<div class="item">
										<div class="col-sm-4 pull-left">
											<b>{{ trans('frontend.order_status') }}</b>
										</div>
										<div class="col-sm-8">
											<b class="text-bold">
												{{ trans('frontend.order_status_'.snake_case(array_get($orders, 'detail.order_status'))) }}
											</b>
										</div>
									</div>
									<div class="item">
										<div class="col-sm-4 pull-left">
											<b>{{ trans('frontend.receive_product') }}</b>
										</div>
										<div class="col-sm-8">
											<b class="text-bold">{{ \App\Bootstrap\Helpers\CartHelper::getPickupTypeNames($orders, null) }}</b>
										</div>
									</div>
									<div class="item">
										<div class="col-sm-4 pull-left">
											<b>{{ trans('frontend.payment_channel') }}</b>
										</div>
										<div class="col-sm-8">
											<b class="text-bold">
												@if (strtolower(array_get($orders, 'detail.payment.payment_gateway_code')) == 'payatstore')
												{!! trans('frontend.pay_at_store_name', ['store_name' => array_get($orders, 'detail.store.name')]) !!}
												@else
												{{ array_get($orders, 'detail.payment.title') }}
												@endif
											</b>
										</div>
									</div>
								</div>
							</address>
						</div>
						<div class="col-sm-6 print-col">
							<div class="scan-barcode">
								<img src="{{ array_get($orders, 'detail.barcode') }}" class="img-responsive">
								<div class="text-center">
									{{ trans('frontend.order_no') }} {{ array_get($orders, 'detail.order_no') }}
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="item-box">
					<div class="row">
						<div class="col-sm-6 print-col">
							<div class="topic-text">
								<b class="text-bold">{{ trans('frontend.order_customer_detail') }}</b>
							</div>
							<address>
								<div class="row">
									<div class="item">
										<div class="col-sm-4 pull-left">
											<b>{{ trans('frontend.shop_name') }}</b>
										</div>
										<div class="col-sm-8">
											<b class="text-bold">{{ array_get($orders, 'detail.buyer.address.content_name') }}</b>
										</div>
									</div>
									<div class="item">
										<div class="col-sm-4 pull-left">
											<b>{{ trans('frontend.buyer_name') }}</b>
										</div>
										<div class="col-sm-8">
											<b class="text-bold">{{ array_get($orders, 'detail.buyer.address.first_name') }} {{ array_get($orders,
												'detail.buyer.address.last_name') }}</b>
										</div>
									</div>
									<div class="item">
										<div class="col-sm-4 pull-left">
											<b>{{ trans('frontend.mobile_phone') }}</b>
										</div>
										<div class="col-sm-8">
											<b class="text-bold">{{ array_get($orders, 'detail.buyer.address.contact_phone') }}</b>
										</div>
									</div>
									<div class="item">
										<div class="col-sm-4 pull-left">
											<b>{{ trans('frontend.email') }}</b>
										</div>
										<div class="col-sm-8">
											<b class="text-bold">{{ array_get($orders, 'detail.buyer.address.contact_email') }}</b>
										</div>
									</div>
									<div class="item">
										<div class="col-sm-4 pull-left">
											<b>{{ trans('frontend.makro_card_id') }}</b>
										</div>
										<div class="col-sm-8">
											<b class="text-bold">{{ empty(array_get($orders, 'detail.buyer.makro_card_id'))?'-':array_get($orders,
												'detail.buyer.makro_card_id') }}</b>
										</div>
									</div>
								</div>
							</address>
						</div>

						@if(!empty(array_get($orders, 'detail.tax_address')))
						<div class="col-sm-6 print-col">
							<div class="item">
								<div class="topic-text">
									<b class="text-bold">{{ trans('frontend.tax_invoice') }}{{--ใบกำกับภาษี--}}</b>
								</div>
								<address>
									<div class="row">
										<div class="item">
											<div class="col-sm-4 pull-left">{{ trans('frontend.company') }}</div>
											<div class="col-sm-8">
												<b class="text-bold">
													{{ array_get($orders, 'detail.buyer.information.business.shop_name') }}
												</b>
											</div>
										</div>
										<div class="item">
											<div class="col-sm-4 pull-left">{{ trans('frontend.branch_no') }}</div>
											<div class="col-sm-8">
												<b class="text-bold">{{ array_get($orders, 'detail.buyer.information.business.branch') }}</b>
											</div>
										</div>
										<div class="item">
											<div class="col-sm-4 pull-left">{{ trans('frontend.tax_id') }}</div>
											<div class="col-sm-8">
												<b class="text-bold">{{ array_get($orders, 'detail.buyer.information.tax_id') }}</b>
											</div>
										</div>
										<div class="item">
											<div class="col-sm-4 pull-left">{{ trans('frontend.address') }}</div>
											<div class="col-sm-8">
												<b class="text-bold">{{ \App\Bootstrap\Helpers\CartHelper::getFullAddress(array_get($orders,
													'detail.tax_address')) }}</b>
											</div>
										</div>
									</div>
									{{--<br>
									<div class="tel">
										<b class="text-bold">{{ trans('frontend.tel') }} : </b> {{ array_get($orders,
										'detail.tax_address.mobile_phone') }}
									</div>
									<div class="mail">
										<b class="text-bold">{{ trans('frontend.email') }} : </b> {{ array_get($orders,
										'detail.tax_address.email_id') }}
									</div>--}}
								</address>
							</div>
						</div>
						@endif
					</div>
				</div>

				<div class="item-box">
					<div class="row">
						@if($orders['detail']['delivery_type'] == 'shipping' || !$orders['detail']['delivery_type'])
						@if(!empty(array_get($orders, 'detail.shipping_address')))
						<div class="col-sm-12 col-xs-12">
							<div class="item">
								<div class="topic-text">
									<b>{{ trans('frontend.order_shipping_address') }}</b>
								</div>

								<address>
									@if(! empty(array_get($orders, 'detail.shipping_address.shop_name')))
									<div class="shop_name">
										<b class="text-bold">{{ array_get($orders, 'detail.shipping_address.shop_name') }}</b>
									</div>
									@endif

									<div class="shop_buyer_name">
										{{ array_get($orders, 'detail.shipping_address.first_name') }} {{ array_get($orders,
										'detail.shipping_address.last_name') }}
									</div>

									<div class="row">
										<div class="item">
											<div class="col-sm-2">{{ trans('frontend.address') }}</div>
											<div class="col-sm-10">
												<b class="text-bold">{{ \App\Bootstrap\Helpers\CartHelper::getFullAddress(array_get($orders,
													'detail.shipping_address')) }}</b>
											</div>
										</div>
										<div class="item tel">
											<div class="col-sm-2">{{ trans('frontend.telephone_number') }}</div>
											<div class="col-sm-10">
												<b class="text-bold">{{ array_get($orders, 'detail.shipping_address.mobile_phone') }}</b>
											</div>
										</div>
										<div class="item mail">
											<div class="col-sm-2">{{ trans('frontend.email') }}</div>
											<div class="col-sm-10">
												<b class="text-bold">{{ array_get($orders, 'detail.shipping_address.email_id')}}</b>
											</div>
										</div>
									</div>
								</address>
							</div>
						</div>
						@endif
						@else
						<div class="col-sm-12 col-xs-12">
							<div class="item">
								<div class="topic-text">
									<b>{{ trans('frontend.order_pickup_receive_at', ['name' => array_get($orders, 'detail.store.name')]) }}</b>
								</div>

								<address>
									<div class="row">
										<div class="item">
											<div class="col-sm-2 pull-left">{{ trans('frontend.order_pickup_makro_store') }}</div>
											<div class="col-sm-10">
												<b class="text-bold">{{ array_get($orders, 'detail.store.name') }}</b>
											</div>
										</div>

										<div class="item">
											<div class="col-sm-2 pull-left">{{ trans('frontend.order_pickup_makro_store_address') }}</div>
											<div class="col-sm-10 ">
												<b class="text-bold">
													{{ array_get($orders, 'detail.store.address.address') }}
													{{ array_get($orders, 'detail.store.address.subdistrict') }}
													{{ array_get($orders, 'detail.store.address.district') }}
													{{ array_get($orders, 'detail.store.address.province') }}
													{{ array_get($orders, 'detail.store.address.postcode') }}
												</b>
												<view-map-button :store-data="{{ json_encode(array_get($orders, 'detail.store')) }}"></view-map-button>
											</div>
										</div>

										<div class="item">
											<div class="col-sm-2 pull-left">{{ trans('frontend.contact_number') }}</div>
											<div class="col-sm-10 ">
												<b class="text-bold">{{ trans('frontend.store_mobile') }}</b>
											</div>
										</div>
										
									</div>
								</address>
							</div>
						</div>
						@endif
					</div>
				</div>
				<?php

						$show_status = true;

						if(in_array($orders['detail']['order_status'], ['Payment Failed', 'Order Expired', 'Order Created'])) {
								$show_status = false;
						}

						$payment_gateway = strtolower(array_get($orders['detail'], 'payment.payment_gateway'));

						if($payment_gateway == '123service' && $orders['detail']['order_status'] == 'Waiting For Payment') {
								$show_status = false;
						}
					?>

				<div class="cart-success margin-bottom-10">
					<cart-success v-bind:cart="{{ htmlspecialchars(json_encode($cart)) }}" v-bind:cart-summary="{{ json_encode($summary) }}"
					 v-bind:promotions="{{ json_encode($promotions) }}" :show-status="'{{ $show_status }}'" :calculate-date="true"
					 :delivery-type="'{{ $orders['detail']['delivery_type']? $orders['detail']['delivery_type']: 'shipping' }}'"
					 :order-date="'{{ $orders['detail']['order_date'] }}'" :config-delivery-date="{{ json_encode(array_get($orders, 'detail.config_delivery_date')) }}">
					</cart-success>
				</div>

				<div class="print hidden-print">
					<re-order-history :products="{{ json_encode($products->toArray()) }}" name="{{ trans('frontend.repeat_your_order')  }}"></re-order-history>
					<a href="javascript:window.print()" class="btn btn-default">
						<i class="far fa-print"></i> {{ __('frontend.print_order') }}
					</a>
				</div>

			</div>
		</div>

	</div>
</div>

@endsection

@section('script-header')

@endsection

@section('script')
@parent

@stop