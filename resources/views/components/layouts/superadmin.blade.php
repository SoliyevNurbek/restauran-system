<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'MyRestaurant_SN Superadmin' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/superadmin.css', 'resources/js/superadmin.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="superadmin-shell min-h-screen font-sans text-slate-800 antialiased">
@php
    $resolvedSetting = \Illuminate\Support\Facades\Schema::hasTable('settings') ? \App\Models\Setting::global() : null;
    $resolvedMediaAssets = \Illuminate\Support\Facades\Schema::hasTable('media_assets') ? \App\Models\MediaAsset::keyed() : collect();
    $brandLogo = $resolvedMediaAssets->get('brand_logo');
    $brandFavicon = $resolvedMediaAssets->get('brand_favicon');
    $brandFaviconUrl = $brandFavicon?->url() ?: ($brandLogo?->url() ?: $resolvedSetting?->logoUrl());
    $currentRoute = request()->route()?->getName();
    $navGroups = [
        'Main' => [
            ['route' => 'superadmin.dashboard', 'label' => 'Dashboard', 'icon' => 'layout-dashboard'],
            ['route' => 'superadmin.businesses.index', 'label' => 'Bizneslar', 'icon' => 'building-2'],
            ['route' => 'superadmin.approvals.index', 'label' => 'Tasdiqlar', 'icon' => 'badge-check'],
            ['route' => 'superadmin.users.index', 'label' => 'Foydalanuvchilar', 'icon' => 'users'],
            ['route' => 'superadmin.subscriptions.index', 'label' => 'Obunalar', 'icon' => 'repeat'],
            ['route' => 'superadmin.plans.index', 'label' => 'Tariflar', 'icon' => 'layers-3'],
            ['route' => 'superadmin.payments.index', 'label' => "To'lovlar", 'icon' => 'credit-card'],
            ['route' => 'superadmin.analytics.index', 'label' => 'Analitika', 'icon' => 'chart-column'],
            ['route' => 'superadmin.notifications.index', 'label' => 'Bildirishnomalar', 'icon' => 'bell'],
        ],
        'Content' => [
            ['route' => 'superadmin.pages.edit', 'label' => 'Sahifalar', 'icon' => 'file-text'],
            ['route' => 'superadmin.languages.edit', 'label' => 'Tillar', 'icon' => 'languages'],
            ['route' => 'superadmin.settings.edit', 'label' => 'Sozlamalar', 'icon' => 'settings-2'],
        ],
        'System' => [
            ['route' => 'superadmin.audit.index', 'label' => 'Audit loglari', 'icon' => 'history'],
            ['route' => 'superadmin.integrations.edit', 'label' => 'Integratsiyalar', 'icon' => 'plug'],
            ['route' => 'superadmin.telegram.edit', 'label' => 'Telegram workflow', 'icon' => 'send'],
            ['route' => 'superadmin.security.index', 'label' => 'Xavfsizlik', 'icon' => 'shield-check'],
        ],
    ];
@endphp
    @if($brandFaviconUrl)
        <link rel="icon" type="image/png" href="{{ $brandFaviconUrl }}">
    @endif
    <div class="min-h-screen lg:flex">
        <div class="fixed inset-0 z-40 hidden bg-slate-950/35 backdrop-blur-sm lg:hidden" data-sidebar-backdrop></div>

        <aside data-sidebar class="fixed inset-y-0 left-0 z-50 flex w-[310px] -translate-x-full flex-col border-r border-white/70 bg-slate-950 text-white shadow-2xl shadow-slate-950/20 transition duration-200 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0">
            <div class="border-b border-white/10 px-6 py-5">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-[1.25rem] bg-white/10 ring-1 ring-white/10">
                        @if($brandLogo?->url())
                            <img src="{{ $brandLogo->url() }}" alt="{{ $resolvedSetting?->restaurant_name ?: 'MyRestaurant_SN' }}" class="h-full w-full object-cover">
                        @else
                            <span class="text-sm font-bold tracking-[0.24em] text-sky-200">MR</span>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-white">{{ $resolvedSetting?->restaurant_name ?? 'MyRestaurant_SN' }}</p>
                        <p class="text-xs text-slate-400">Premium superadmin control center</p>
                    </div>
                </div>
            </div>

            <div class="sa-sidebar-scroll flex-1 space-y-8 overflow-y-auto px-4 py-6">
                @foreach($navGroups as $group => $items)
                    <section>
                        <p class="px-3 text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">{{ $group }}</p>
                        <div class="mt-3 space-y-1.5">
                            @foreach($items as $item)
                                @php
                                    $routeParams = match ($item['route']) {
                                        'superadmin.languages.edit' => ['lang' => request('lang', 'uz')],
                                        'superadmin.pages.edit' => ['slug' => request('slug', \App\Models\Page::TERMS_OF_USE)],
                                        default => [],
                                    };
                                    $isActive = request()->routeIs(str_replace('.index', '.*', $item['route'])) || request()->routeIs($item['route']);
                                @endphp
                                <a href="{{ route($item['route'], $routeParams) }}" class="group flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-medium transition {{ $isActive ? 'bg-white text-slate-950 shadow-lg shadow-slate-950/10' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl {{ $isActive ? 'bg-slate-100 text-slate-900' : 'bg-white/5 text-slate-400 group-hover:text-white' }}">
                                        <i data-lucide="{{ $item['icon'] }}" class="h-4 w-4"></i>
                                    </span>
                                    <span class="truncate">{{ $item['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>

            <div class="border-t border-white/10 px-4 py-4">
                <div class="rounded-3xl border border-white/10 bg-white/5 px-4 py-4">
                    <div class="flex items-start gap-3">
                        <span class="mt-0.5 flex h-10 w-10 items-center justify-center rounded-2xl bg-sky-500/10 text-sky-300">
                            <i data-lucide="send" class="h-4 w-4"></i>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-white">Telegram monitoring</p>
                            <p class="mt-1 text-xs leading-5 text-slate-400">Muhim alertlarni mobil qurilmaga uzatish uchun integratsiyani sozlang.</p>
                            <a href="{{ route('superadmin.integrations.edit') }}" class="mt-3 inline-flex text-xs font-semibold text-sky-300">Integratsiyani ochish</a>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <div class="min-h-screen flex-1 lg:min-w-0">
            <header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/80 backdrop-blur-xl">
                <div class="mx-auto flex max-w-[1680px] items-center justify-between gap-4 px-4 py-4 sm:px-6">
                    <div class="flex min-w-0 items-center gap-3">
                        <button type="button" data-sidebar-toggle class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700 shadow-sm lg:hidden">
                            <i data-lucide="menu" class="h-5 w-5"></i>
                        </button>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
                                <span>MyRestaurant_SN</span>
                                <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i>
                                <span>{{ $pageTitle ?? 'Superadmin' }}</span>
                            </div>
                            <h1 class="truncate text-xl font-semibold text-slate-950">{{ $pageTitle ?? 'Superadmin' }}</h1>
                            <p class="truncate text-sm text-slate-500">{{ $pageSubtitle ?? 'Platforma operatsion nazorati va premium boshqaruv markazi.' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('superadmin.notifications.index') }}" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-600 shadow-sm hover:text-slate-950">
                            <i data-lucide="bell" class="h-4 w-4"></i>
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-900/15">
                                <i data-lucide="log-out" class="h-4 w-4"></i>
                                <span>Chiqish</span>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="sa-main-scroll mx-auto max-w-[1680px] px-4 py-6 sm:px-6">
                @if (session('success'))
                    <div data-dismissible class="mb-5 flex items-start justify-between gap-4 rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-900 shadow-sm">
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                                <i data-lucide="check-circle-2" class="h-4 w-4"></i>
                            </span>
                            <div>
                                <p class="font-semibold">Amal muvaffaqiyatli bajarildi</p>
                                <p class="mt-1">{{ session('success') }}</p>
                            </div>
                        </div>
                        <button type="button" data-dismiss-parent class="text-emerald-700"><i data-lucide="x" class="h-4 w-4"></i></button>
                    </div>
                @endif

                @if (session('error'))
                    <div data-dismissible class="mb-5 flex items-start justify-between gap-4 rounded-3xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-900 shadow-sm">
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-2xl bg-rose-100 text-rose-700">
                                <i data-lucide="shield-alert" class="h-4 w-4"></i>
                            </span>
                            <div>
                                <p class="font-semibold">Jarayonda xatolik yuz berdi</p>
                                <p class="mt-1">{{ session('error') }}</p>
                            </div>
                        </div>
                        <button type="button" data-dismiss-parent class="text-rose-700"><i data-lucide="x" class="h-4 w-4"></i></button>
                    </div>
                @endif

                @if (session('generated_password'))
                    <div class="mb-5 rounded-3xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900 shadow-sm">
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                                <i data-lucide="key-round" class="h-4 w-4"></i>
                            </span>
                            <div>
                                <p class="font-semibold">Bir martalik kirish paroli yaratildi</p>
                                <p class="mt-1"><strong>{{ session('generated_password') }}</strong></p>
                            </div>
                        </div>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
