@extends('layouts.main')

@section('nav-mobile-menu')

@endsection

@section('content')
    <?php
        $images = collect($brand['images']);
        $imagesA = $images->filter(function ($value, $key) {
            return str_is('A*', $value['position']);
        })->first();
    ?>

    @if (! empty($imagesA))
        <div class="container margin-bottom">
            @if (! empty($imagesA['url']))
                <a href="{{ $imagesA['url'] }}" target="{{ array_get($imagesA, 'target', '_self') }}">
                    @endif
                    <img src="{{ $imagesA['image'] }}" class="img-responsive">
                    @if (! empty($imagesA['url']))
                </a>
            @endif
        </div>
    @endif

    <!--================================================== -->

    <div class="container margin-bottom">
        <div class="col-lg-3 col-md-3 padding-right-15 hidden-sm hidden-xs">
            @include('partials.product.sidebar_filter', ['filters' => $filters ? $filters : [], 'notShowBrandFilter' => true])
        </div>

        <a name="product-list"></a>
        @include('partials.product.list', ['title' => $brand['name'], 'products' => $products, 'listName' => env('GA_BRAND_LIST')])
    </div>

    <!--================================================== -->
@endsection

@section('script')
    <script>
        @if (! $products->isEmpty())
                IMPRESSION_PRODUCTS.push({
                name: '{{ env('GA_BRAND_LIST', 'Brand list') }}',
                items: {}
            })
            @foreach($products as $product)
                GLOBAL_PRUDUCTS['{{ $product['id'] }}'] = {!! json_encode($product) !!}
                IMPRESSION_PRODUCTS[0].items['{{ $product['id'] }}'] = {
                'refer_object': 'GLOBAL_PRUDUCTS',
                'id': '{{ $product['id'] }}'
            }
            @endforeach
        @endif

        gaTriggerImpression()
    </script>
@endsection