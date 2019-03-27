<div class="search-filter-box">
    @if (! empty($allCampaigns))
        @include('partials.product.sidebar-campaign')
    @endif

    @if (! empty($categoriesTree))
        <product-category-filter v-bind:categories="{{ json_encode($categoriesTree) }}" v-bind:filter="false"></product-category-filter>
    @elseif (! empty($filters['category']['items']))
        <product-category-filter v-bind:categories="{{ json_encode($filters['category']['items']) }}"></product-category-filter>
    @endif

        <product-price-filter></product-price-filter>

        @if (isset($filters['package']) && !empty($filters['package']))
            <product-filter
                    v-bind:items="{{ json_encode($filters['package']['items']) }}"
                    id-name="filter_package"
                    dispatch="filterSelectPackage"
                    title="{{ __('frontend.package') }}"
                    selected-state="packages"
            >
            </product-filter>
        @endif

        @if (isset($filters['capacity']) && !empty($filters['capacity']))
            <product-filter
                    v-bind:items="{{ json_encode($filters['capacity']['items']) }}"
                    id-name="filter_capacity"
                    dispatch="filterSelectCapacity"
                    title="{{ __('frontend.capacity') }}"
                    selected-state="capacities"
            >
            </product-filter>
        @endif

        @if (isset($filters['color']) && !empty($filters['color']))
            <product-filter-color
                    v-bind:items="{{ json_encode($filters['color']['items']) }}"
                    id-name="filter_color"
                    dispatch="filterSelectColor"
                    title="{{ __('frontend.color') }}"
                    selected-state="colors"
            >
            </product-filter-color>
        @endif

        @if (isset($filters['size']) && !empty($filters['size']))
            <product-filter
                    v-bind:items="{{ json_encode($filters['size']['items']) }}"
                    id-name="filter_size"
                    dispatch="filterSelectSize"
                    title="{{ __('frontend.size') }}"
                    selected-state="sizes"
            >
            </product-filter>
        @endif

        @if ((empty($notShowBrandFilter) || ! $notShowBrandFilter))
            @if (isset($filters['brand']) && !empty($filters['brand']))
                <product-filter
                        v-bind:items="{{ json_encode($filters['brand']['items']) }}"
                        id-name="filter_brand"
                        dispatch="filterSelectBrand"
                        title="{{ __('frontend.brand') }}"
                        selected-state="brands"
                >
                </product-filter>
            @endif
        @endif
</div>