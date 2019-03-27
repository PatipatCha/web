<div class="search-filter-menu-box">
    <div class="search-filter-text">
        <b>{{ trans('frontend.category') }}</b>
    </div>
    <div class="search-filter-text2">
        <div class="menu-related-categories">
            <ul class="category-level-0">
                @foreach($categoriesTree as $level0)
                    @if (! empty($level0['children']))
                        <li class="category-level-0 have-children @if(! empty($category_level_0) && $category_level_0['id'] == $level0['id']) active @endif">
                            <a href="javascript:void(0);">{{ $level0['name'] }}</a>
                            <ul class="category-level-1">
                                @foreach($level0['children'] as $level1)
                                    @if (! empty($level1['children']))
                                        <li class="category-level-1 have-children @if(! empty($category_level_1) && $category_level_1['id'] == $level1['id']) active @endif">
                                            <a href="javascript:void(0);">{{ $level1['name'] }}</a>
                                            <ul class="category-level-2">
                                                @foreach($level1['children'] as $level2)
                                                    <li class="category-level-2 no-children @if(! empty($category_level_2) && $category_level_2['id'] == $level2['id']) active @endif">
                                                        <a href="{{ route('categories.show', ['slug' => $level2['slug']]) }}">{{ $level2['name'] }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @else
                                        <li class="category-level-1 no-children @if(! empty($category_level_1) && $category_level_1['id'] == $level1['id']) active @endif">
                                            <a href="{{ route('categories.show', ['slug' => $level1['slug']]) }}">{{ $level1['name'] }}</a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </li>
                    @else
                        <li class="category-level-0 no-children @if(! empty($category_level_0) && $category_level_0['id'] == $level0['id']) active @endif">
                            <a href="{{ route('categories.show', ['slug' => $level0['slug']]) }}">{{ $level0['name'] }}</a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
</div>