@foreach($children as $child)

<?php
    $childrenOfChild = null;
    if (! empty($child['children'])) {
        $childrenOfChild = collect($child['children']['data']);
        $childrenOfChild = $childrenOfChild->sortBy('priority');
    }
?>

<div class="container margin-bottom hidden-lg hidden-md">
    <div class="dropdown">
        <button class="dropbtn-by-product">
            {{ $child['name'] }}&nbsp;&nbsp;&nbsp;
            <img src="{{ asset('assets/images/icon-Dropdown-W.png') }}" width="10">
        </button>
        <div class="dropdown-content">
            @if (! empty($childrenOfChild))
                @foreach($childrenOfChild as $subchild)
                    <a href="{{ route('categories.show', ['slug' => $subchild['slug']]) }}" title="{{ $subchild['name'] }}">{{ $subchild['name'] }}</a>
                @endforeach
            @endif
        </div>
    </div>
</div>

<div class="container margin-bottom box-categories-container">
    <div class="box-categories-scroll">
        <div class="box-menu-catagories">
            <div class="box-menu-catagories2 col-lg-3 col-md-3 no-padding hidden-sm hidden-xs">
                <div class="menu-catagories-list">
                    {{ $child['name'] }}
                </div>
                <div class="box-line1"></div>

                <?php
                    $images = collect($child['images']);
                    $imagesD1 = $images->filter(function ($value, $key) {
                        return str_is('D1*', $value['position']);
                    })->all();
                    $imagesD2 = $images->where('position', 'D2')->first();
                    $imagesD3 = $images->where('position', 'D3')->first();
                    $imagesD4 = $images->where('position', 'D4')->first();
                    $imagesD5 = $images->where('position', 'D5')->first();
                    $imagesD6 = $images->where('position', 'D6')->first();
                ?>

                @if (! empty($childrenOfChild))
                    <?php $i = 1; ?>
                    @foreach($childrenOfChild as $subchild)
                        <?php if ($i++ > 10) { break; } ?>
                        <a href="{{ route('categories.show', ['slug' => $subchild['slug']]) }}" title="{{ $subchild['name'] }}">
                            <div class="menu-catagories-list2">
                                {{ $subchild['name'] }}
                            </div>
                        </a>
                    @endforeach
                @endif
            </div>

            <div class="col-sm-3 no-padding box-banner box-banner-left">
                @if (! empty($imagesD1))
                    <div id="carousel-example-generic_{{ $child['id'] }}" class="carousel slide" data-ride="carousel">
                        @if (count($imagesD1) > 1)
                        <!-- Indicators -->
                        <ol class="carousel-indicators">
                            @foreach ($imagesD1 as $key => $value)
                                <li data-target="#carousel-example-generic_{{ $child['id'] }}" data-slide-to="{{ $key }}" class="{{ ($key == 0) ? 'active' : '' }}"></li>
                            @endforeach
                        </ol>
                        @endif

                        <!-- Wrapper for slides -->
                        <div class="carousel-inner" role="listbox">
                            @foreach ($imagesD1 as $key => $value)
                                <div class="item {{ ($key == 0) ? 'active' : '' }}">
                                    <a href="{{ $value['url'] }}" target="{{ array_get($value, 'target', '_self') }}"><img src="{{ $value['image'] }}" class="img-responsive"></a>
                                </div>
                            @endforeach
                        </div>

                        <!-- @if (count($imagesD1) > 1) -->
                            <!-- Controls -->
                            <!-- <a class="left carousel-control" href="#carousel-example-generic_{{ $child['id'] }}" role="button" data-slide="prev">
                                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                                <span class="sr-only">{{ trans('frontend.previous') }}</span>
                            </a>
                            <a class="right carousel-control" href="#carousel-example-generic_{{ $child['id'] }}" role="button" data-slide="next">
                                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                                <span class="sr-only">{{ trans('frontend.next') }}</span>
                            </a> -->
                        <!-- @endif -->
                    </div>
                @endif
            </div>

            <div class="col-sm-4 no-padding box-banner box-banner-center">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                    @if (! empty($imagesD2))
                        <a href="{{ $imagesD2['url'] }}" target="{{ array_get($imagesD2, 'target', '_self') }}"><img src="{{ $imagesD2['image'] }}" class="img-responsive"/></a>
                    @endif
                </div>

                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 no-padding">
                    @if (! empty($imagesD4))
                        <a href="{{ $imagesD4['url'] }}" target="{{ array_get($imagesD4, 'target', '_self') }}"><img src="{{ $imagesD4['image'] }}" class="img-responsive"/></a>
                    @endif
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 no-padding">
                    @if (! empty($imagesD5))
                        <a href="{{ $imagesD5['url'] }}" target="{{ array_get($imagesD5, 'target', '_self') }}"><img src="{{ $imagesD5['image'] }}" class="img-responsive"/></a>
                    @endif
                </div>
            </div>

            <div class="col-sm-2 no-padding box-banner box-banner-right">
                @if (! empty($imagesD3))
                    <a href="{{ $imagesD3['url'] }}" target="{{ array_get($imagesD3, 'target', '_self') }}"><img src="{{ $imagesD3['image'] }}" class="img-responsive"/></a>
                @endif

                @if (! empty($imagesD6))
                    <a href="{{ $imagesD6['url'] }}" target="{{ array_get($imagesD6, 'target', '_self') }}"><img src="{{ $imagesD6['image'] }}" class="img-responsive"/></a>
                @endif
                <!--</div>-->
            </div>
            <div class="clearfix"></div>
        </div>
    </div>

    @if($images->isNotEmpty())
    <div class="control visible-xs hide">
        <div class="arrow prev">
            <i class="fa fa-chevron-left"></i>
        </div>
        <div class="arrow next hide">
            <i class="fa fa-chevron-right"></i>
        </div>
    </div>
    @endif
</div>
@endforeach