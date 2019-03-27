@foreach($products as $product)
<div class="box-product-bg box-hover-border">
	<div class="box-product clearfix">
    @if (! empty($product['ribbon']))
    <div class="ribbon-status">
        @if ($product['ribbon']['type'] == 'static')
        <img src="{{ $product['ribbon']['image']['medium'] }}">
        @endif
    </div>
    @endif
    <div class="col-lg-3 col-md-3 col-sm-3 no-padding">
        <a href="{{ route('products.show-by-slug', ['slug' => $product['slug']]) }}" title="{{ $product['name'] }}"
           onclick="gaOnProductWasClicked(this, {{ $product['id'] }}, '{{ isset($listName) ? $listName : '' }}'); return !ga.loaded;">
            <div class="thumb-product">
                <img src="{{ $product['thumbnail'] }}" class="img-responsive">
            </div>
        </a>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 no-padding">
        <div class="text-detail2">
					<div class="text-pd-name">
							<a href="{{ route('products.show-by-slug', ['slug' => $product['slug']]) }}" title="{{ $product['name'] }}"
							onclick="gaOnProductWasClicked(this, {{ $product['id'] }}, '{{ isset($listName) ? $listName : '' }}'); return !ga.loaded;">
									{{ $product['name'] }}
							</a>
					</div>
					<div class="text-pd">
							{{ trans('frontend.product_code') }} {{ $product['item_id'] }}
					</div>
					<div class="text-pd-red">
							{{ number_format($product['price'], 2) }} ฿
					</div>

					@if ($product['normal_price'] > $product['price'])
							<div class="text-pd">
									<strike>{{ number_format($product['normal_price'], 2) }} ฿</strike>
							</div>
					@endif

					<div class="text-pd">
							{{ trans('frontend.avg_price_per_unit', ['unit_name' => $product['unit_name'], 'price' => number_format($product['average_price_per_unit'], 2)]) }}
					</div>
			</div>
    </div>
    <div class="col-lg-5 col-md-5 col-sm-5 no-padding">
        <div class="box-product-text-detail2 hidden-xs padding-right-ad">
            {{--<div class="text-pd-2 hide">
                Ranking
                <span style="color:#FFBD00; font-size:12px;" class="glyphicon glyphicon-star" aria-hidden="true"></span>
                <span style="color:#FFBD00; font-size:12px;" class="glyphicon glyphicon-star" aria-hidden="true"></span>
                <span style="color:#FFBD00; font-size:12px;" class="glyphicon glyphicon-star" aria-hidden="true"></span>
                <span style="color:#FFBD00; font-size:12px;" class="glyphicon glyphicon-star" aria-hidden="true"></span>
                <span style="color:#FFBD00; font-size:12px;" class="glyphicon glyphicon-star" aria-hidden="true"></span>
                <span style="color:#FFBD00; font-size:12px;" class="glyphicon glyphicon-star" aria-hidden="true"></span>
            </div>
            <div class="text-pd-2 hide">
                Feedback
                <span style="color:#F01616;">1,342</span>
                Ordered
                <span style="color:#F01616;">30,231</span>
            </div>--}}
            <div class="text-pd-2">
                @if (! empty($product['brand']))
                    <a href="{{ route('brands.show-by-slug', ['slug' => $product['brand']['slug']]) }}">{{ $product['brand']['name'] }}</a>
                @endif

                @if (! empty($product['tags']))
                    |
                    @foreach($product['tags'] as $key => $tag)
                        @if ($key > 0)
                            ,&nbsp;
                        @endif
                        <a href="{{ route('search.index', ['q' => htmlspecialchars("#{$tag}")]) }}">#{{ $tag }}</a>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="box-b-cw">
            <div class="box-b-cart pull-left">
                <add-to-cart-button :button_type="2" product="{!! htmlspecialchars(json_encode($product)) !!}"></add-to-cart-button>
            </div>


            <div class="box-b-wishlist pull-right">
                <add-to-wish-list-button product="{!! htmlspecialchars(json_encode($product)) !!}" v-bind:reload-when-remove="true"></add-to-wish-list-button>
            </div>

        </div>
    </div>
  </div>
  <div class="clearfix"></div>
</div>
@endforeach