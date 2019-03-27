<div class="search-filter-menu-box">
    <div class="search-filter-text">
        <b>{{ trans('frontend.campaign') }}</b>
    </div>
    <div class="search-filter-text2">
        <div class="menu-related-categories">
            <ul class="campaign">
                @foreach($allCampaigns as $allCampaign)
                    <li class="@if($allCampaign['id'] == $campaign['id']) active @endif">
                        <a href="{{ route('campaigns.show', ['slug' => $allCampaign['slug']]) }}">{{ $allCampaign['name'] }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>