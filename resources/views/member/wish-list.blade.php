@extends('layouts.main')

@section('nav-mobile-menu')

@endsection

@section('content')
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

                    <div class="topic-text topic-text-top title clearfix">

                        <b class="title">{{ trans('frontend.my_wishlist') }}</b>
                        @if (!$wishlists->isEmpty())
                            <div class="pull-right re-order">
                                <re-order-wish-list name="{{ trans('frontend.add_all_to_cart') }}"></re-order-wish-list>
                            </div>
                        @endif
                    </div>
                    <div class="deta-box4 wishlist">

                        @if ($wishlists->isEmpty())
                            <div class="empty-box text-center">
                                <img src="{{ asset('assets/images/icon-wishlist-100px.png') }}"/>
                                <h4>{{ trans('frontend.wishlist_is_empty') }}</h4>
                                <a href="{{ route('home.index') }}"
                                   class="btn btn-primary">{{ __('frontend.choose_product') }}</a>
                            </div>
                        @else
                            <div class="col-lg-12 list-view">
                                @include('partials/product/item-' . (empty($itemType) ? 'default' : $itemType), ['products' => $wishlists, 'listName' => env('GA_MEMBER_WISH_LIST_PRODUCT')])
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-lg-12 padding-top15">
                                <div class="pull-right">
                                    <re-order-wish-list
                                            name="{{ trans('frontend.add_all_to_cart') }}"></re-order-wish-list>
                                </div>
                            </div>
                        @endif

                    </div>

                    <div class="pagination col-lg-9 col-md-9 col-sm-12 pull-right no-padding hidden-xs">
                        {!! $wishlists->appends(request()->query())->render() !!}
                    </div>

                    <div class="pagination col-sm-12 col-xs-12 no-padding visible-xs">
                        {!! $wishlists->appends(request()->query())->render() !!}
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>

@endsection

@section('script-header')

@endsection

@section('script')
    @parent

    <script>
        @if (!$wishlists->isEmpty())
        IMPRESSION_PRODUCTS.push({
            name: '{{ env('GA_MEMBER_WISH_LIST_PRODUCT', 'Wish list products') }}',
            items: {}
        })

        @foreach($wishlists as $data)
            GLOBAL_PRUDUCTS['{{ $data['id'] }}'] = {!! json_encode($data) !!}
            IMPRESSION_PRODUCTS[0].items['{{ $data['id'] }}'] = {
            'refer_object': 'GLOBAL_PRUDUCTS',
            'id': '{{ $data['id'] }}'
        }
        @endforeach

        gaTriggerImpression()
        @endif
    </script>

@stop