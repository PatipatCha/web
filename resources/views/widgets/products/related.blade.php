@if(!$products->isEmpty())
<div class="container margin-bottom">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  no-padding">
        <div class="promotion-header">
            <div class="row">
                <div class="col-sm-8 col-xs-6 promotion-title">
                    {{ trans('frontend.related_product_view') }}
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
            <div class="owl-carousel" id="product-related">
                @foreach($products as $product)
                    <div class="box-hover-border">
                        <div class="box-product">
                            <a href="{{ route('products.show-by-slug', ['slug' => $product['slug']]) }}" title="{{ $product['name'] }}"
                                onclick="gaOnProductWasClicked(this, {{ $product['id'] }}, '{{ isset($listName) ? $listName : '' }}'); return !ga.loaded;">
                                <div class="thumb-product">
                                    <img src="{{ $product['thumbnail'] }}" class="img-responsive">
                                </div>
                            </a>
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
                            <div class="box-b-cw">
                                <div class="box-b-cart pull-left">
                                    <add-to-cart-button :button_type="2" product="{!! htmlspecialchars(json_encode($product)) !!}"></add-to-cart-button>
                                </div>
                                <div class="box-b-wishlist pull-right">
                                    <add-to-wish-list-button product="{!! htmlspecialchars(json_encode($product)) !!}"></add-to-wish-list-button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        @if (! empty($product['ribbon']))
                            <div class="ribbon-status">
                                @if ($product['ribbon']['type'] == 'static')
                                    <img src="{{ $product['ribbon']['image']['medium'] }}" width="55">
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@push('custom-scripts')
    <script>
        $(window).ready(function() {
            var owl = null;

            var width = getViewportDimension()[0];
            var responsiveItems = 2;
            if (width < 600) {
                responsiveItems = 2;
            } else {
                responsiveItems = {{ $show }};
            }

            IMPRESSION_PRODUCTS.push({
                'name': '{{ env('GA_RELATED_PRODUCT_LIST', 'Related product list') }}',
                'items': {}
            })
            let index = IMPRESSION_PRODUCTS.length-1
            @foreach($products as $data)
                GLOBAL_PRUDUCTS['{{ $data['id'] }}'] = {
                        'refer_object': 'CAROUSEL_PRODUCTS',
                        'id': '{{ $data['id'] }}'
                    };
                CAROUSEL_PRODUCTS['{{ $data['id'] }}'] = {!! json_encode($data) !!}
                IMPRESSION_PRODUCTS[index]['items']['{{ $data['id'] }}'] = {
                    'refer_object': 'CAROUSEL_PRODUCTS',
                    'id': '{{ $data['id'] }}'
                }
            @endforeach

            var len = {{ count($products) }};
            var loop = false
            if (len <= responsiveItems) {
                loop = false
            } else {
                loop = true
            }

            owl = $('#product-related')
            owl.owlCarousel({
                loop: loop,
                nav: true,
                responsive: {
                    0: {
                        items: 2
                    },
                    600: {
                        items: {{ $show }}
                    },
                    1900: {
                        items: {{ $show }}
                    }
                },
                onInitialized: function (event) {
                    if (event.item.count <= event.page.size) {
                        $('.owl-nav', $(event.target)).hide()
                    }
                }
            });

            gaTriggerImpression();
        });
    </script>
    @endpush

    @endif