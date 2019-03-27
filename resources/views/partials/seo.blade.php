@if (! empty($seo_description))
<meta name='description' itemprop='description' content='{!! strip_tags($seo_description) !!}' />
@endif

@if (! empty($seo_keywords))
<meta name='keywords' content='{!! strip_tags($seo_keywords) !!}' />
@endif