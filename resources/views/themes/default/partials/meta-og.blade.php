{{--
    Meta Open Graph + Twitter Card.
    Variabel: title, description, image (URL absolut/relatif), type ('website'|'article'),
    url, publishedTime (ISO 8601). Semua opsional; $settings dibagikan lewat ThemeDataComposer.
--}}
@php
    $siteName = $settings->site_name ?? config('app.name');
    $ogTitle = filled($title ?? null) ? $title : $siteName;
    $ogDescription = \Illuminate\Support\Str::limit(trim(strip_tags($description ?? ($settings->meta_description ?? ''))), 200);
    $ogType = $type ?? 'website';
    $ogUrl = $url ?? url()->current();
    $ogImage = filled($image ?? null) ? url($image) : null;
@endphp
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:title" content="{{ $ogTitle }}">
@if(filled($ogDescription))
<meta property="og:description" content="{{ $ogDescription }}">
@endif
<meta property="og:url" content="{{ $ogUrl }}">
@if($ogImage)
<meta property="og:image" content="{{ $ogImage }}">
@endif
@if($ogType === 'article' && filled($publishedTime ?? null))
<meta property="article:published_time" content="{{ $publishedTime }}">
@endif
<meta name="twitter:card" content="{{ $ogImage ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:title" content="{{ $ogTitle }}">
@if(filled($ogDescription))
<meta name="twitter:description" content="{{ $ogDescription }}">
@endif
@if($ogImage)
<meta name="twitter:image" content="{{ $ogImage }}">
@endif
