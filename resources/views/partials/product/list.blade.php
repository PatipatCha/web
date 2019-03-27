<div class=" col-lg-9 col-md-9 no-padding">
    <div class="title-name">
        <b>{{ $title }}</b> <span class="total-amount-items">{{ trans('frontend.total_amount_items', ['amount' => empty($total_items) ? 0 : $total_items]) }}</span>
    </div>

    @include('partials.product.filter_sorter')

    <div class="clearfix"></div>

    @if (! $products->isEmpty())
        <div class="col-lg-12 no-padding" id="product-layout-container" v-bind:class="layoutClass">
            <?php $i = 1; ?>
                @include('partials/product/item-' . (empty($itemType) ? 'default' : $itemType), ['products' => $products, 'listName' => (isset($listName) ? $listName :'')])

            <div class="pagination col-lg-12 no-padding hidden-xs">
                {!! $products->appends(request()->query())->render() !!}
            </div>

            <div class="pagination col-xs-12 no-padding visible-xs">
                {!! $products->appends(request()->query())->render() !!}
            </div>
        </div>
    @else
        <div class="col-lg-12 no-padding" id="product-layout-container">
            {{ trans('frontend.not_found_product') }}
        </div>
    @endif

    <div class="clearfix"></div>
</div>
<div class="clearfix"></div>

