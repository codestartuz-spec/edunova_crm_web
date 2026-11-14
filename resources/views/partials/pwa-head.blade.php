<link rel="manifest" href="{{ route('pwa.manifest') }}">
<meta name="theme-color" content="{{ \App\Support\BrandTheme::currentHex() ?? '#5d46e2' }}">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="{{ \App\Support\Brand::short() }}">
<link rel="apple-touch-icon" href="{{ \App\Support\Brand::iconUrl(180) }}">
<script>window.PWA_SW = @js(route('sw'));</script>
