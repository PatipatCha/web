@extends('layouts.main')

@section('nav-mobile-menu')

@endsection

@section('content')
    {{--<div class="container margin-bottom hidden-lg hidden-md">
        <div class="dropdown-product-list-menu">
            <button class="dropbtn-product-list-menu">
                <b>Fresh Food</b>&nbsp;&nbsp;&nbsp;
                <img src="{{ asset('assets/images/icon-Dropdown-B.png') }}" width="10"></button>
            <div class="dropdown-content-product-list-menu">
                <a href="#">Meat</a>
                <a href="#">Fruit</a>
                <a href="#">Pork</a>
                <a href="#">Meatball</a>
                <a href="#">Noodle</a>
            </div>
        </div>
    </div>--}}

    <div class="container margin-bottom">
        <div class="col-lg-3 col-md-3 padding-right-15 hidden-sm hidden-xs">
            @include('partials.product.sidebar_filter', ['filters' => $filters ? $filters : []])
        </div>

        <a name="product-list"></a>
        @include('partials.product.list', ['title' => $page_name, 'products' => $products, 'listName' => env('GA_SEARCH_RESULT')])
    </div>

    <!--================================================== -->
@endsection

@section('script')
    <script>
        @if (! $products->isEmpty())
                IMPRESSION_PRODUCTS.push({
                name: '{{ env('GA_SEARCH_RESULT', 'Search result') }}',
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