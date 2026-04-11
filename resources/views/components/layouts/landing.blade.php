<!doctype html>
<html lang="{{ request('lang', 'uz') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $description ?? "restaran-system restoran, to'yxona va banket zallari uchun bron, mijozlar, to'lovlar va boshqaruv jarayonlarini raqamlashtiruvchi professional platforma." }}">
    <meta name="theme-color" content="#0b1120">
    <meta property="og:title" content="{{ $title ?? "restaran-system | Restoran va to'yxonalar uchun professional boshqaruv platformasi" }}">
    <meta property="og:description" content="{{ $description ?? "Restoran, to'yxona va banket biznesi uchun bron, CRM, to'lov, analitika va operatsion nazoratni birlashtirgan zamonaviy platforma." }}">
    <meta property="og:type" content="website">
    <title>{{ $title ?? 'restaran-system | Premium boshqaruv platformasi' }}</title>
    @php
        $resolvedSetting = \Illuminate\Support\Facades\Schema::hasTable('settings')
            ? \App\Models\Setting::global()
            : null;
        $resolvedMediaAssets = \Illuminate\Support\Facades\Schema::hasTable('media_assets')
            ? \App\Models\MediaAsset::keyed()
            : collect();
        $landingFavicon = $resolvedMediaAssets->get('brand_favicon');
        $landingFaviconUrl = $landingFavicon?->url()
            ?: ($resolvedSetting?->faviconUrl() ?: ($resolvedMediaAssets->get('brand_logo')?->url() ?: $resolvedSetting?->logoUrl()));
    @endphp
    @if($landingFaviconUrl)
        <link rel="icon" type="image/png" href="{{ $landingFaviconUrl }}">
        <link rel="shortcut icon" href="{{ $landingFaviconUrl }}">
    @endif
    @php
        $landingCss = public_path('assets/landing/landing.css');
        $landingJs = public_path('assets/landing/landing.js');
        $landingCssVersion = file_exists($landingCss) ? filemtime($landingCss) : time();
        $landingJsVersion = file_exists($landingJs) ? filemtime($landingJs) : time();
    @endphp
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Cormorant+Garamond:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/landing/landing.css') }}?v={{ $landingCssVersion }}">
    <script src="{{ asset('assets/landing/landing.js') }}?v={{ $landingJsVersion }}" defer></script>
</head>
<body class="landing-body">
    {{ $slot }}
</body>
</html>
