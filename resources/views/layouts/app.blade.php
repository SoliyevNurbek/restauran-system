<!doctype html>
<html lang="uz" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Restoran CRM' }}</title>
    <script>
        (() => {
            const theme = localStorage.getItem('theme') || '{{ $appSetting->theme_preference ?? 'light' }}';
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
    <style>[x-cloak]{display:none!important;}</style>
</head>
<body class="h-full bg-slate-100 text-slate-700 dark:bg-slate-950 dark:text-slate-100">
@php
    $navGroups = [
        [
            'label' => 'Asosiy',
            'items' => [
                ['route' => 'dashboard', 'label' => 'Bosh sahifa', 'icon' => 'layout-dashboard'],
            ],
        ],
        [
            'label' => 'Ombor',
            'key' => 'inventory',
            'items' => [
                ['route' => 'suppliers.index', 'label' => 'Ta\'minotchilar', 'icon' => 'truck'],
                ['route' => 'products.index', 'label' => 'Mahsulotlar', 'icon' => 'package-search'],
                ['route' => 'purchases.index', 'label' => 'Kirimlar', 'icon' => 'package-plus'],
            ],
        ],
        [
            'label' => 'Xarajatlar',
            'key' => 'expenses',
            'items' => [
                ['route' => 'expenses.index', 'label' => 'Xarajatlar', 'icon' => 'wallet-cards'],
                ['route' => 'expense-categories.index', 'label' => 'Kategoriyalar', 'icon' => 'folder-tree'],
            ],
        ],
        [
            'label' => 'Tahlil',
            'key' => 'analysis',
            'items' => [
                ['route' => 'reports.index', 'label' => 'Hisobotlar', 'icon' => 'bar-chart-3'],
                ['route' => 'settings.edit', 'label' => 'Sozlamalar', 'icon' => 'settings'],
            ],
        ],
    ];
@endphp

<div class="min-h-full" x-data="{sidebarOpen:false, groups:{inventory: {{ request()->routeIs('suppliers.*') || request()->routeIs('products.*') || request()->routeIs('purchases.*') ? 'true' : 'false' }}, expenses: {{ request()->routeIs('expenses.*') || request()->routeIs('expense-categories.*') ? 'true' : 'false' }}, analysis: {{ request()->routeIs('reports.*') || request()->routeIs('settings.*') ? 'true' : 'false' }}}}">
    <div x-cloak x-show="sidebarOpen" class="fixed inset-0 z-40 bg-slate-950/45 lg:hidden" @click="sidebarOpen = false"></div>

    <aside class="fixed inset-y-0 left-0 z-50 flex w-80 flex-col bg-white/95 shadow-soft backdrop-blur transition duration-300 dark:bg-slate-900/95"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
        <div class="border-b border-slate-200 px-5 py-5 dark:border-slate-800">
            <div class="flex items-center gap-3 rounded-3xl bg-primary-600 px-4 py-4 text-white">
                @if(!empty($appSetting?->logo_path))
                    <img src="{{ asset('storage/'.$appSetting->logo_path) }}" alt="Logo" class="h-12 w-12 rounded-2xl object-cover">
                @else
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15 p-2">
                        <img src="{{ asset('Javohirlogo.png') }}" alt="Logo" class="h-full w-full object-contain">
                    </div>
                @endif
                <div class="min-w-0">
                    <p class="text-xs uppercase tracking-[0.25em] text-white/70">CRM</p>
                    <p class="truncate text-sm font-semibold">{{ $appSetting->restaurant_name ?? 'Restoran CRM' }}</p>
                </div>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto px-4 py-4">
            <nav class="space-y-5">
                @foreach($navGroups as $group)
                    <div class="space-y-2">
                        <p class="px-3 text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">{{ $group['label'] }}</p>
                        @if(!empty($group['key']))
                            <button type="button"
                                    class="flex w-full items-center justify-between rounded-2xl px-3 py-2.5 text-left text-sm font-medium text-slate-700 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800"
                                    @click="groups['{{ $group['key'] }}'] = !groups['{{ $group['key'] }}']">
                                <span class="flex items-center gap-3">
                                    <i data-lucide="chevrons-up-down" class="h-4 w-4 text-slate-400"></i>
                                    {{ $group['label'] }}
                                </span>
                                <i data-lucide="chevron-down" class="h-4 w-4 transition" :class="groups['{{ $group['key'] }}'] ? 'rotate-180' : ''"></i>
                            </button>

                            <div x-show="groups['{{ $group['key'] }}']" x-cloak class="space-y-1 pl-2">
                                @foreach($group['items'] as $item)
                                    <a href="{{ route($item['route']) }}"
                                       class="flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs(str_replace('.index', '.*', $item['route'])) || request()->routeIs($item['route']) ? 'bg-primary-600 text-white shadow-soft' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                                        <i data-lucide="{{ $item['icon'] }}" class="h-4 w-4"></i>
                                        {{ $item['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        @else
                            @foreach($group['items'] as $item)
                                <a href="{{ route($item['route']) }}"
                                   class="flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs(str_replace('.index', '.*', $item['route'])) || request()->routeIs($item['route']) ? 'bg-primary-600 text-white shadow-soft' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                                    <i data-lucide="{{ $item['icon'] }}" class="h-4 w-4"></i>
                                    {{ $item['label'] }}
                                </a>
                            @endforeach
                        @endif
                    </div>
                @endforeach
            </nav>
        </div>
    </aside>

    <div class="lg:pl-80">
        <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 px-4 py-4 backdrop-blur dark:border-slate-800 dark:bg-slate-900/90 md:px-8">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <button type="button" @click="sidebarOpen = !sidebarOpen" class="rounded-xl border border-slate-200 p-2 text-slate-500 dark:border-slate-700 dark:text-slate-300 lg:hidden">
                        <i data-lucide="menu" class="h-5 w-5"></i>
                    </button>
                    <div>
                        <h1 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $pageTitle ?? 'Panel' }}</h1>
                        <p class="text-xs text-slate-500">Restoran uchun kirim, xarajat va ta'minot nazorati</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button id="themeToggle" type="button" class="rounded-xl border border-slate-200 p-2 text-slate-500 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                        <i data-lucide="moon" class="h-4 w-4 dark:hidden"></i>
                        <i data-lucide="sun" class="hidden h-4 w-4 dark:block"></i>
                    </button>

                    <div class="hidden text-right md:block">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500">{{ auth()->user()->username }}</p>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-primary-700">Chiqish</button>
                    </form>
                </div>
            </div>
        </header>

        <main class="p-4 md:p-8">
            @if(session('success'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-300">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-300">
                    <p class="font-medium">Formani qayta tekshiring.</p>
                    <ul class="mt-2 list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
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
