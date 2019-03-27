<div class="col-md-3 col-lg-3 no-padding box-banner box-banner-left">
    <!-- A1 -->
    <div id="{{ $id }}" class="carousel slide" data-ride="carousel">

        @if (! empty($banners['A1']) && count($banners['A1']) > 1)
        <!-- Indicators -->
        <ol class="carousel-indicators">
            <?php $i = 0; ?>
            @foreach ($banners['A1'] as $index => $item)
                <li data-target="#{{ $id }}" data-slide-to="{{ $i }}" class="{{ $i == 0 ? 'active' : '' }}"></li>
                <?php ++$i; ?>
            @endforeach
        </ol>
        @endif

        <!-- Wrapper for slides -->
        <div class="carousel-inner" role="listbox">
            @if (isset($banners['A1']) && !empty($banners['A1']))
                <?php $i = 0; ?>
                @foreach ($banners['A1'] as $index => $item)
                    <div class="item {{ $i == 0 ? 'active' : '' }}">
                        <a href="{{ $item['redirect_url'] }}" <?php echo !(empty($item['target'])) ? 'target="' . $item['target'] . '"' : '' ?>><img src="{{ $item['image_url'] }}" class="img-responsive"></a>
                    </div>
                    <?php ++$i; ?>
                @endforeach
            @endif
        </div>

        <!-- @if (! empty($banners['A1']) && count($banners['A1']) > 1) -->
            <!-- Controls -->
            <!-- <a class="left carousel-control" href="#{{ $id }}" role="button" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" href="#{{ $id }}" role="button" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a> -->
        <!-- @endif -->
    </div>
</div>

<div class="col-md-4 col-lg-4 no-padding box-banner box-banner-center">
    <!-- A2 -->
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        @if (isset($banners['A2']) && !empty($banners['A2']))
            <a href="{{ $banners['A2'][0]['redirect_url'] }}" <?php echo !(empty($banners['A2'][0]['target'])) ? 'target="' . $banners['A2'][0]['target'] . '"' : '' ?>><img src="{{ $banners['A2'][0]['image_url'] }}" class="img-responsive banner-A2"/></a>
        @endif
    </div>

    <!--<div class="col-lg-12 col-md-12 no-padding">-->
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 no-padding">
        <!-- A4 -->
        @if (isset($banners['A4']) && !empty($banners['A4']))
            <a href="{{ $banners['A4'][0]['redirect_url'] }}" <?php echo !(empty($banners['A4'][0]['target'])) ? 'target="' . $banners['A4'][0]['target'] . '"' : '' ?>><img src="{{ $banners['A4'][0]['image_url'] }}" class="img-responsive banner-A4"/></a>
        @endif
    </div>

    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 no-padding">
        <!-- A5 -->
        @if (isset($banners['A5']) && !empty($banners['A5']))
            <a href="{{ $banners['A5'][0]['redirect_url'] }}" <?php echo !(empty($banners['A5'][0]['target'])) ? 'target="' . $banners['A5'][0]['target'] . '"' : '' ?>><img src="{{ $banners['A5'][0]['image_url'] }}" class="img-responsive banner-A5"/></a>
        @endif
    </div>
    <!--</div>-->
</div>

<div class="col-md-2 col-lg-2 no-padding box-banner box-banner-right">
    <!-- A3 -->
    @if (isset($banners['A3']) && !empty($banners['A3']))
        <a href="{{ $banners['A3'][0]['redirect_url'] }}" <?php echo !(empty($banners['A3'][0]['target'])) ? 'target="' . $banners['A3'][0]['target'] . '"' : '' ?>><img src="{{ $banners['A3'][0]['image_url'] }}" class="img-responsive banner-A3"/></a>
    @endif

    <!-- A6 -->
    @if (isset($banners['A6']) && !empty($banners['A6']))
        <a href="{{ $banners['A6'][0]['redirect_url'] }}" <?php echo !(empty($banners['A6'][0]['target'])) ? 'target="' . $banners['A6'][0]['target'] . '"' : '' ?>><img src="{{ $banners['A6'][0]['image_url'] }}" class="img-responsive banner-A6"/></a>
    @endif
</div>
<!--</div>-->

<div class="clearfix"></div>