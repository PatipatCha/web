@foreach ($campaigns as $campaign)
@if (! empty($campaign))
<div class="container margin-bottom">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  no-padding campaign">
        <div class="promotion-header">
            <div class="row">
                <div class="col-sm-8 col-xs-6 promotion-title">
                    {{ $campaign['name'] }}
                </div>
                <div class="col-sm-4 col-xs-6">
                    <a href="{{ route('campaigns.show', ['slug' => $campaign['slug']]) }}" class="btn btn-default btn-xs pull-right">{{ trans('frontend.view_all') }}</a>
                </div>
            </div>
        </div>

        <div class="promotion-name hidden">
            {{ $campaign['name'] }}
        </div>

        @if (! empty($campaign['bannerB']))
        <div class="col-lg-3 col-md-3 no-padding visible-lg">
            <div class="promotion-BN bg-banner-superdeal">
                <a href="{{ route('campaigns.show', ['slug' => $campaign['slug']]) }}">
                    <img src="{{ $campaign['bannerB'] }}" class="img-responsive">
                    <div class="promotion-BN-hover">
                        <div class="promotion-BN-text">
                            {{ trans('frontend.view_all') }}
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="col-lg-9 col-md-12 col-sm-12 col-xs-12 no-padding">
            @if (! empty($campaign['products']))
            <div class="owl-carousel" id="home-campaign-{{ $campaign['id'] }}">
                @include('partials.product.home-campaign-block', ['products' => $campaign['products']])
            </div>
            @endif
        </div>
        @else
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
            @if (! empty($campaign['products']))
            <div class="owl-carousel" id="home-campaign-{{ $campaign['id'] }}">
                @include('partials.product.home-campaign-block', ['products' => $campaign['products']])
            </div>
            @endif
        </div>
        @endif
    </div>

    <div class="box-b-promotion-viewall col-xs-12 hidden">
        <a href="{{ route('campaigns.show', ['slug' => $campaign['slug']]) }}">
            <div class="b-promotion-viewall">
                {{ trans('frontend.view_all') }}
                <i class="fa fa-angle-right"></i>
                {{-- <img src="{{ asset('assets/images/icon-Slides2.png') }}" width="10"> --}}
            </div>
        </a>
    </div>   
</div>
@endif
@endforeach