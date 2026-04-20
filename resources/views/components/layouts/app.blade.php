@props(['title' => null, 'pageTitle' => null, 'pageSubtitle' => null])

<!doctype html>
<html lang="uz" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Restoran CRM' }}</title>
    @php
        $resolvedSetting = \Illuminate\Support\Facades\Schema::hasTable('settings')
            ? \App\Models\Setting::currentFor(auth()->user())
            : null;
        $resolvedMediaAssets = \Illuminate\Support\Facades\Schema::hasTable('media_assets')
            ? \App\Models\MediaAsset::keyed(auth()->user())
            : collect();
    $brandLogo = $resolvedMediaAssets->get('brand_logo');
    $brandFavicon = $resolvedMediaAssets->get('brand_favicon');
    $brandLogoUrl = $brandLogo?->url() ?: $resolvedSetting?->logoUrl();
    $brandFaviconUrl = $brandFavicon?->url()
            ?: $resolvedSetting?->faviconUrl()
            ?: $brandLogoUrl;
    $tenantSubscription = null;
    $tenantRoleLabel = match (auth()->user()?->role) {
        'superadmin' => 'Superadmin',
        'admin' => 'Egasi',
        default => auth()->user()?->role ? ucfirst((string) auth()->user()?->role) : 'Operator',
    };

    if (auth()->check()
        && ! auth()->user()?->isSuperAdmin()
        && \Illuminate\Support\Facades\Schema::hasTable('business_subscriptions')
        && \Illuminate\Support\Facades\Schema::hasTable('subscription_plans')
    ) {
        $tenantSubscription = \App\Models\BusinessSubscription::query()
            ->with('plan')
            ->where('venue_connection_id', auth()->user()?->venue_connection_id)
            ->latest('starts_at')
            ->first();
    }

    $subscriptionStatusLabel = match ($tenantSubscription?->status) {
        'trial' => 'Trial',
        'active' => 'Active',
        'expired' => 'Expired',
        'suspended' => 'Suspended',
        'canceled' => 'Canceled',
        default => "Noma'lum",
    };
    $subscriptionStatusClasses = match ($tenantSubscription?->status) {
        'trial' => 'bg-blue-100 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300',
        'active' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300',
        'expired' => 'bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-300',
        'suspended' => 'bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300',
        'canceled' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
        default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
    };
    $nextBillingDate = $tenantSubscription?->renews_at ?? $tenantSubscription?->trial_ends_at ?? $tenantSubscription?->expires_at;
    $remainingDays = $nextBillingDate ? max(now()->startOfDay()->diffInDays($nextBillingDate->copy()->startOfDay(), false), 0) : null;
@endphp
    @if($brandFaviconUrl)
        <link rel="icon" type="image/png" href="{{ $brandFaviconUrl }}">
        <link rel="shortcut icon" href="{{ $brandFaviconUrl }}">
    @endif
    <script>
        (() => {
            const theme = localStorage.getItem('theme') || '{{ $resolvedSetting->theme_preference ?? 'light' }}';
            if (theme === 'dark') document.documentElement.classList.add('dark');
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'ui-sans-serif', 'system-ui']
                    },
                    colors: {
                        primary: {
                            50: '#eff7f1',
                            100: '#daefdf',
                            200: '#b8debf',
                            300: '#89c195',
                            400: '#58a46a',
                            500: '#3e8550',
                            600: '#316a40',
                            700: '#285534',
                            800: '#23442c',
                            900: '#1e3825'
                        }
                    },
                    boxShadow: {
                        soft: '0 18px 40px -18px rgba(15, 23, 42, 0.22)'
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        [x-cloak]{display:none!important;}

        html,
        body {
            min-height: 100%;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .responsive-actions {
            display: flex;
            gap: 0.5rem;
            max-width: 100%;
        }

        .mobile-fit-table,
        .mobile-wrap-strip {
            max-width: 100%;
        }

        @media (max-width: 640px) {
            .responsive-actions {
                flex-direction: column;
                align-items: flex-start;
            }

            .responsive-actions > a,
            .responsive-actions > button,
            .responsive-actions > form,
            .responsive-actions > form > button {
                max-width: 100%;
            }

            .responsive-actions > a,
            .responsive-actions > button,
            .responsive-actions > form > button {
                display: inline-flex;
                justify-content: center;
            }

            .responsive-actions .inline-flex {
                flex-wrap: nowrap !important;
            }

            .mobile-fit-table {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
            }

            .mobile-fit-table table {
                width: max-content !important;
                min-width: 100% !important;
                table-layout: auto !important;
            }

            .mobile-fit-table th,
            .mobile-fit-table td {
                white-space: nowrap !important;
                word-break: normal;
                overflow-wrap: normal;
            }

            .mobile-wrap-strip {
                display: flex;
                flex-wrap: wrap;
                overflow-x: visible !important;
            }

            .mobile-wrap-strip > * {
                min-width: 0 !important;
                max-width: 100%;
            }
        }

        @media (max-width: 1024px) {
            main,
            main > *,
            main section,
            main article,
            main aside,
            main form,
            main div {
                min-width: 0;
                max-width: 100%;
            }

            main img,
            main svg,
            main canvas,
            main video,
            main iframe {
                max-width: 100%;
                height: auto;
            }

            main .overflow-x-auto {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            main table {
                width: max-content;
                min-width: 100%;
                table-layout: auto;
            }

            main th,
            main td {
                white-space: normal !important;
                word-break: break-word;
                overflow-wrap: anywhere;
                vertical-align: top;
            }

            main .whitespace-nowrap {
                white-space: normal !important;
            }

            main td > .flex:not(.responsive-actions),
            main th > .flex:not(.responsive-actions),
            main .inline-flex {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body class="min-h-full bg-slate-100 text-slate-700 dark:bg-slate-950 dark:text-slate-100">
@php
    $navGroups = [
        [
            'label' => 'Dashboard',
            'group_icon' => 'layout-dashboard',
            'items' => [
                ['route' => 'dashboard', 'label' => 'Bosh sahifa', 'icon' => 'layout-dashboard'],
            ],
        ],
        [
            'label' => "To'y boshqaruvi",
            'key' => 'events',
            'group_icon' => 'calendar-days',
            'items' => [
                ['route' => 'bookings.index', 'label' => 'Bronlar', 'icon' => 'clipboard-list'],
                ['route' => 'calendar.index', 'label' => 'Kalendar', 'icon' => 'calendar-days'],
                ['route' => 'clients.index', 'label' => 'Mijozlar', 'icon' => 'users'],
                ['route' => 'halls.index', 'label' => 'Zallar', 'icon' => 'building-2'],
                ['route' => 'event-types.index', 'label' => 'Tadbir turlari', 'icon' => 'party-popper'],
                ['route' => 'wedding-packages.index', 'label' => "To'y paketlari", 'icon' => 'gift'],
            ],
        ],
        [
            'label' => 'Moliya',
            'key' => 'finance',
            'group_icon' => 'wallet',
            'items' => [
                ['route' => 'payments.index', 'label' => "Mijoz to'lovlari", 'icon' => 'wallet'],
                ['route' => 'purchases.index', 'label' => 'Kirimlar', 'icon' => 'package-plus'],
                ['route' => 'inventory-expenses.index', 'label' => 'Xarajatlar', 'icon' => 'receipt-text'],
                ['route' => 'suppliers.index', 'label' => 'Qarzdorlik va balans', 'icon' => 'hand-coins'],
                ['route' => 'billing.payments.index', 'label' => "To'lovlar", 'icon' => 'credit-card'],
                ['route' => 'billing.subscriptions.index', 'label' => 'Obunalar', 'icon' => 'repeat'],
                ['route' => 'billing.plans.index', 'label' => 'Tariflar', 'icon' => 'layers-3'],
            ],
        ],
        [
            'label' => 'Ombor',
            'key' => 'inventory',
            'group_icon' => 'package-search',
            'items' => [
                ['route' => 'products.index', 'label' => 'Mahsulotlar', 'icon' => 'package-search'],
                ['route' => 'inventory-expense-categories.index', 'label' => 'Kategoriyalar', 'icon' => 'folder-tree'],
                ['route' => 'booking-usage-items.index', 'label' => 'Ombor harakati', 'icon' => 'arrow-left-right'],
                ['route' => 'dashboard', 'label' => 'Minimal qoldiq', 'icon' => 'triangle-alert', 'params' => ['focus' => 'low-stock']],
                ['route' => 'suppliers.index', 'label' => 'Ta\'minotchilar', 'icon' => 'truck'],
            ],
        ],
        [
            'label' => 'Xodimlar',
            'key' => 'staff',
            'group_icon' => 'badge-check',
            'items' => [
                ['route' => 'employees.index', 'label' => 'Foydalanuvchilar', 'icon' => 'users'],
                ['route' => 'employees.index', 'label' => 'Rollar va ruxsatlar', 'icon' => 'shield-check', 'params' => ['tab' => 'roles']],
                ['route' => 'employees.index', 'label' => 'Faollik', 'icon' => 'activity', 'params' => ['tab' => 'activity']],
            ],
        ],
        [
            'label' => 'Tahlil',
            'items' => [
                ['route' => 'reports.index', 'label' => 'Tahlil', 'icon' => 'bar-chart-3'],
            ],
        ],
        [
            'label' => 'Sozlamalar',
            'items' => [
                ['route' => 'settings.edit', 'label' => 'Sozlamalar', 'icon' => 'settings-2'],
            ],
        ],
    ];
    $pageSubtitleText = $pageSubtitle;
@endphp

<div class="min-h-screen" x-data="{
    sidebarOpen:false,
    activeGroup:
        {{ request()->routeIs('bookings.*') || request()->routeIs('event-types.*') || request()->routeIs('halls.*') || request()->routeIs('wedding-packages.*') || request()->routeIs('clients.*') || request()->routeIs('calendar.*') ? '\'events\'' : (request()->routeIs('payments.*') || request()->routeIs('purchases.*') || request()->routeIs('inventory-expenses.*') || request()->routeIs('suppliers.*') || request()->routeIs('billing.*') || request()->routeIs('plans.index') || request()->routeIs('subscriptions.index') ? '\'finance\'' : (request()->routeIs('products.*') || request()->routeIs('booking-usage-items.*') || request()->routeIs('inventory-expense-categories.*') ? '\'inventory\'' : (request()->routeIs('employees.*') ? '\'staff\'' : (request()->routeIs('reports.*') ? '\'analytics\'' : (request()->routeIs('settings.*') ? '\'settings\'' : 'null'))))) }},
    scrollToActiveGroup() {
        if (!this.activeGroup) return;
        this.$nextTick(() => this.$refs[`group-${this.activeGroup}`]?.scrollIntoView({ block: 'nearest', behavior: 'smooth' }));
    },
    toggleGroup(key) {
        this.activeGroup = this.activeGroup === key ? null : key;
        this.scrollToActiveGroup();
    }
}" x-init="scrollToActiveGroup()">
    <div x-cloak x-show="sidebarOpen" class="fixed inset-0 z-40 bg-slate-950/45 lg:hidden" @click="sidebarOpen = false"></div>

    <aside class="fixed inset-y-0 left-0 z-50 flex w-[min(86vw,320px)] max-w-[320px] flex-col border-r border-slate-200/70 bg-white/96 shadow-soft backdrop-blur transition duration-300 dark:border-slate-800 dark:bg-slate-900/96 xl:w-[330px] xl:max-w-[330px]"
           x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="-translate-x-full opacity-80"
           x-transition:enter-end="translate-x-0 opacity-100"
           x-transition:leave="transition ease-in duration-200"
           x-transition:leave-start="translate-x-0 opacity-100"
           x-transition:leave-end="-translate-x-full opacity-80"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
        <div class="px-4 pb-3 pt-4 sm:px-5 sm:pt-5">
            <div class="flex h-[92px] items-center justify-center gap-4 rounded-[28px] border border-slate-200 bg-white px-4 shadow-sm dark:border-slate-800 dark:bg-slate-950/70 sm:h-[96px]">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-900 sm:h-16 sm:w-16">
                    @if($brandLogoUrl)
                        <img src="{{ $brandLogoUrl }}" alt="Logo" class="h-full w-full object-cover">
                    @else
                        <span class="text-sm font-bold text-primary-700 dark:text-primary-300">MR</span>
                    @endif
                </div>

                <div class="min-w-0 flex-1">
                    <p class="truncate text-base font-semibold leading-tight text-slate-900 dark:text-white">{{ $resolvedSetting?->restaurant_name ?: 'MyRestaurant_SN' }}</p>
                </div>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto px-3 pb-4 sm:px-4">
            <nav class="space-y-4">
                @foreach($navGroups as $group)
                    <div @if(!empty($group['key'])) x-ref="group-{{ $group['key'] }}" @endif>
                        @if(!empty($group['key']))
                            <button type="button"
                                    class="flex w-full items-center justify-between rounded-2xl border border-slate-200/80 bg-slate-50 px-3 py-2.5 text-left text-sm font-medium text-slate-700 transition hover:bg-white dark:border-slate-800 dark:bg-slate-950/50 dark:text-slate-200 dark:hover:bg-slate-900"
                                    @click="toggleGroup('{{ $group['key'] }}')">
                                <span class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-white text-slate-500 shadow-sm dark:bg-slate-800 dark:text-slate-300">
                                        <i data-lucide="{{ $group['group_icon'] ?? 'panel-left-close' }}" class="h-4 w-4"></i>
                                    </span>
                                    {{ $group['label'] }}
                                </span>
                                <i data-lucide="chevron-down" class="h-4 w-4 transition" :class="activeGroup === '{{ $group['key'] }}' ? 'rotate-180' : ''"></i>
                            </button>

                            <div
                                x-show="activeGroup === '{{ $group['key'] }}'"
                                x-cloak
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-1"
                                class="space-y-1.5 overflow-hidden pt-2"
                            >
                                @foreach($group['items'] as $item)
                                    <a href="{{ route($item['route'], $item['params'] ?? []) }}"
                                       @click="if (window.innerWidth < 1024) sidebarOpen = false"
                                       class="group relative flex items-center gap-3 overflow-hidden rounded-2xl px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs(str_replace('.index', '.*', $item['route'])) || request()->routeIs($item['route']) ? 'bg-primary-600 text-white shadow-soft ring-1 ring-primary-500/30' : 'text-slate-600 hover:bg-slate-100 hover:shadow-sm dark:text-slate-300 dark:hover:bg-slate-900' }}">
                                        <span class="absolute inset-y-2 left-1 w-1 rounded-full {{ request()->routeIs(str_replace('.index', '.*', $item['route'])) || request()->routeIs($item['route']) ? 'bg-white/90' : 'bg-transparent group-hover:bg-primary-200 dark:group-hover:bg-primary-800' }}"></span>
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl transition-all duration-200 {{ request()->routeIs(str_replace('.index', '.*', $item['route'])) || request()->routeIs($item['route']) ? 'bg-white/15 text-white' : 'bg-white text-slate-500 shadow-sm group-hover:scale-105 group-hover:bg-primary-50 group-hover:text-primary-700 dark:bg-slate-800 dark:text-slate-300 dark:group-hover:bg-slate-800 dark:group-hover:text-primary-300' }}">
                                            <i data-lucide="{{ $item['icon'] }}" class="h-4 w-4"></i>
                                        </span>
                                        <span class="min-w-0 flex-1 truncate">{{ $item['label'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            @foreach($group['items'] as $item)
                                <a href="{{ route($item['route'], $item['params'] ?? []) }}"
                                   @click="if (window.innerWidth < 1024) sidebarOpen = false"
                                   class="group relative flex items-center gap-3 overflow-hidden rounded-2xl px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs(str_replace('.index', '.*', $item['route'])) || request()->routeIs($item['route']) ? 'bg-primary-600 text-white shadow-soft ring-1 ring-primary-500/30' : 'text-slate-600 hover:bg-slate-100 hover:shadow-sm dark:text-slate-300 dark:hover:bg-slate-900' }}">
                                    <span class="absolute inset-y-2 left-1 w-1 rounded-full {{ request()->routeIs(str_replace('.index', '.*', $item['route'])) || request()->routeIs($item['route']) ? 'bg-white/90' : 'bg-transparent group-hover:bg-primary-200 dark:group-hover:bg-primary-800' }}"></span>
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl transition-all duration-200 {{ request()->routeIs(str_replace('.index', '.*', $item['route'])) || request()->routeIs($item['route']) ? 'bg-white/15 text-white' : 'bg-white text-slate-500 shadow-sm group-hover:scale-105 group-hover:bg-primary-50 group-hover:text-primary-700 dark:bg-slate-800 dark:text-slate-300 dark:group-hover:bg-slate-800 dark:group-hover:text-primary-300' }}">
                                        <i data-lucide="{{ $item['icon'] }}" class="h-4 w-4"></i>
                                    </span>
                                    <span class="min-w-0 flex-1 truncate">{{ $item['label'] }}</span>
                                </a>
                            @endforeach
                        @endif
                    </div>
                @endforeach
            </nav>
        </div>
    </aside>

    <div class="min-w-0 lg:pl-[320px] xl:pl-[330px]">
        <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/92 px-3 py-3 backdrop-blur dark:border-slate-800 dark:bg-slate-900/92 sm:px-4 md:px-6 xl:px-8">
            <div class="flex items-start justify-between gap-3">
                <div class="flex min-w-0 flex-1 items-center gap-3">
                    <button type="button" @click="sidebarOpen = !sidebarOpen" class="rounded-xl border border-slate-200 p-2 text-slate-500 dark:border-slate-700 dark:text-slate-300 lg:hidden">
                        <i data-lucide="menu" class="h-5 w-5"></i>
                    </button>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">
                            <span>Admin panel</span>
                            <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                            <span>{{ now()->translatedFormat('d M Y') }}</span>
                        </div>
                        <h1 class="mt-1 truncate text-xl font-semibold text-slate-900 dark:text-white">{{ $pageTitle ?? 'Panel' }}</h1>
                        @if(filled($pageSubtitleText))
                            <p class="mt-1 text-sm text-slate-500">{{ $pageSubtitleText }}</p>
                        @endif
                    </div>
                </div>

                <div class="ml-auto flex shrink-0 items-start gap-2 sm:items-center sm:gap-3">
                    @if($tenantSubscription)
                        <div class="hidden rounded-[24px] border border-slate-200 bg-slate-50 px-4 py-3 text-left shadow-sm dark:border-slate-800 dark:bg-slate-950/60 xl:block">
                            <div class="flex items-center gap-2">
                                <i data-lucide="badge-check" class="h-4 w-4 text-slate-500"></i>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Tarif</p>
                            </div>
                            <div class="mt-2 flex items-center gap-2">
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $tenantSubscription->plan?->name ?? 'Basic' }}</p>
                                <span class="rounded-full px-2 py-1 text-[11px] font-semibold {{ $subscriptionStatusClasses }}">{{ $subscriptionStatusLabel }}</span>
                            </div>
                            <p class="mt-1 text-xs text-slate-500">
                                Keyingi to'lov: {{ $nextBillingDate?->format('d M Y') ?? "Noma'lum" }}
                                @if(! is_null($remainingDays))
                                    | {{ $remainingDays }} kun
                                @endif
                            </p>
                        </div>
                    @endif

                    <button id="themeToggle" type="button" class="rounded-xl border border-slate-200 p-2 text-slate-500 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                        <i data-lucide="moon" class="h-4 w-4 dark:hidden"></i>
                        <i data-lucide="sun" class="hidden h-4 w-4 dark:block"></i>
                    </button>

                    <div class="hidden text-right md:block">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ auth()->user()?->name ?? 'Foydalanuvchi' }}</p>
                        <p class="text-xs text-slate-500">{{ $tenantRoleLabel }} | {{ auth()->user()?->username ?? 'guest' }}</p>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="rounded-2xl bg-slate-900 px-3 py-2 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200 sm:px-4">Chiqish</button>
                    </form>
                </div>
            </div>
        </header>

        <main class="min-w-0 p-3 sm:p-4 md:p-6 xl:p-8">
            @if(session('success'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-300">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-300">
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>

<div id="confirmModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/50 p-4">
    <div class="w-full max-w-sm rounded-3xl bg-white p-6 shadow-soft dark:bg-slate-900">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Amalni tasdiqlang</h3>
        <p class="mt-2 text-sm text-slate-500">Bu amalni ortga qaytarib bo'lmaydi.</p>
        <div class="mt-6 flex justify-end gap-2">
            <button id="cancelConfirm" type="button" class="rounded-xl border border-slate-200 px-4 py-2 text-sm dark:border-slate-700">Bekor qilish</button>
            <button id="acceptConfirm" type="button" class="rounded-xl bg-red-600 px-4 py-2 text-sm text-white">O'chirish</button>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();

    document.getElementById('themeToggle')?.addEventListener('click', () => {
        document.documentElement.classList.toggle('dark');
        const theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
        localStorage.setItem('theme', theme);
        document.querySelectorAll('input[name="theme_preference"]').forEach(el => el.value = theme);
    });

    let currentForm = null;
    const modal = document.getElementById('confirmModal');

    document.querySelectorAll('[data-confirm-form]').forEach(button => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            currentForm = button.closest('form');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    });

    document.getElementById('cancelConfirm')?.addEventListener('click', () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        currentForm = null;
    });

    document.getElementById('acceptConfirm')?.addEventListener('click', () => {
        if (currentForm) currentForm.submit();
    });

    document.querySelectorAll('form[data-loading-form]').forEach((form) => {
        form.addEventListener('submit', () => {
            const submitButton = form.querySelector('button[type="submit"]');
            if (!submitButton || submitButton.disabled) return;
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>';
        });
    });
</script>
</body>
</html>
