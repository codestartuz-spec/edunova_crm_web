{{-- v25: ochiq sahifalar uchun SEO teglari. Kerak: $title, $description, $canonical, $locale; ixtiyoriy: $alternates, $jsonLd --}}
<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1">
<link rel="canonical" href="{{ $canonical }}">
@foreach (($alternates ?? []) as $hreflang => $href)
    <link rel="alternate" hreflang="{{ $hreflang }}" href="{{ $href }}">
@endforeach
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ \App\Support\Brand::name() }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:image" content="{{ \App\Support\Seo::ogImage() }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:locale" content="{{ \App\Support\Seo::LOCALES[$locale] ?? 'uz_UZ' }}">
@foreach (\App\Support\Seo::LOCALES as $code => $og)
    @if ($code !== $locale)<meta property="og:locale:alternate" content="{{ $og }}">@endif
@endforeach
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ \App\Support\Seo::ogImage() }}">
@if ($g = config('seo.google_verification'))<meta name="google-site-verification" content="{{ $g }}">@endif
@if ($y = config('seo.yandex_verification'))<meta name="yandex-verification" content="{{ $y }}">@endif
@if ($b = config('seo.bing_verification'))<meta name="msvalidate.01" content="{{ $b }}">@endif
@isset($jsonLd)
<script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
@endisset
