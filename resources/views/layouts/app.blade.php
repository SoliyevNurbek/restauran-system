<!doctype html>
<html lang="uz" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Restoran boshqaruv tizimi' }}</title>
    <script>
        (() => {
            const theme = localStorage.getItem('theme') || '{{ $appSetting->theme_preference ?? 'light' }}';
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
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
                            50: '#f4faf6',
                            100: '#e6f4eb',
                            200: '#cce9d6',
                            300: '#a8d7b8',
                            400: '#7fbe95',
                            500: '#4f9f6c',
                            600: '#3f8458',
                            700: '#336948',
                            800: '#2c543c',
                            900: '#254533'
                        }
                    },
                    boxShadow: {
                        soft: '0 10px 25px -5px rgba(0, 0, 0, 0.08)'
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="h-full bg-slate-100 text-slate-700 dark:bg-slate-950 dark:text-slate-200">
<div class="min-h-full" x-data="{sidebarOpen:false}">
    <div class="fixed inset-y-0 left-0 z-50 w-72 transform bg-white/95 p-4 shadow-soft transition duration-300 dark:bg-slate-900/95 lg:translate-x-0"
         :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
        <div class="flex items-center gap-3 rounded-2xl bg-primary-500 p-3 text-white">
            @if(!empty($appSetting?->logo_path))
                <img src="{{ asset('storage/'.$appSetting->logo_path) }}" alt="logo" class="h-10 w-10 rounded-xl object-cover">
            @else
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 font-bold">R</div>
            @endif
            <div>
                <p class="text-xs uppercase tracking-widest text-white/70">Restoran</p>
                <p class="text-sm font-semibold">{{ $appSetting->restaurant_name ?? 'Green Fork' }}</p>
            </div>
        </div>

        <nav class="mt-6 space-y-1">
            @php
                $menu = [
                    ['route' => 'dashboard', 'label' => 'Bosh sahifa', 'icon' => 'layout-dashboard'],
                    ['route' => 'orders.index', 'label' => 'Buyurtmalar', 'icon' => 'clipboard-list'],
                    ['route' => 'menu-items.index', 'label' => 'Menyu (Taomlar)', 'icon' => 'utensils-crossed'],
                    ['route' => 'categories.index', 'label' => 'Kategoriyalar', 'icon' => 'layers-3'],
                    ['route' => 'tables.index', 'label' => 'Stollar', 'icon' => 'armchair'],
                    ['route' => 'customers.index', 'label' => 'Mijozlar', 'icon' => 'users'],
                    ['route' => 'staff.index', 'label' => 'Xodimlar', 'icon' => 'badge-check'],
                    ['route' => 'reports.index', 'label' => 'Hisobotlar', 'icon' => 'bar-chart-3'],
                    ['route' => 'settings.edit', 'label' => 'Sozlamalar', 'icon' => 'settings'],
                ];
            @endphp

            @foreach($menu as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition {{ request()->routeIs(str_replace('.index', '.*', $item['route'])) || request()->routeIs($item['route']) ? 'bg-primary-500 text-white' : 'text-slate-600 hover:bg-primary-100 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                    <i data-lucide="{{ $item['icon'] }}" class="h-4 w-4"></i>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
    </div>

    <div class="lg:pl-72">
        <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 px-4 py-3 backdrop-blur dark:border-slate-800 dark:bg-slate-900/95 md:px-8">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <button type="button" @click="sidebarOpen = !sidebarOpen" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800 lg:hidden">
                        <i data-lucide="menu" class="h-5 w-5"></i>
                    </button>
                    <div>
                        <h1 class="text-lg font-semibold text-slate-800 dark:text-white">{{ $pageTitle ?? 'Admin panel' }}</h1>
                        <p class="text-xs text-slate-500">Restoran boshqaruv markazi</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button id="themeToggle" type="button" class="rounded-lg border border-slate-200 p-2 text-slate-500 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                        <i data-lucide="moon" class="h-4 w-4 dark:hidden"></i>
                        <i data-lucide="sun" class="hidden h-4 w-4 dark:block"></i>
                    </button>

                    <div class="hidden text-right sm:block">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500 capitalize">{{ auth()->user()->role }}</p>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="rounded-lg bg-primary-600 px-3 py-2 text-sm font-medium text-white hover:bg-primary-700">Chiqish</button>
                    </form>
                </div>
            </div>
        </header>

        <main class="p-4 md:p-8">
            @if(session('success'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300">
                    {{ session('success') }}
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>

<div id="confirmModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/50 p-4">
    <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-soft dark:bg-slate-900">
        <h3 class="text-lg font-semibold">Amalni tasdiqlang</h3>
        <p class="mt-2 text-sm text-slate-500">Bu amalni ortga qaytarib bo'lmaydi.</p>
        <div class="mt-6 flex justify-end gap-2">
            <button id="cancelConfirm" type="button" class="rounded-lg border border-slate-200 px-4 py-2 text-sm dark:border-slate-700">Bekor qilish</button>
            <button id="acceptConfirm" type="button" class="rounded-lg bg-red-600 px-4 py-2 text-sm text-white">O'chirish</button>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();

    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
            const theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
            localStorage.setItem('theme', theme);
            document.querySelectorAll('input[name="theme_preference"]').forEach(el => el.value = theme);
        });
    }

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
        if (currentForm) {
            currentForm.submit();
        }
    });

    document.querySelectorAll('form[data-loading-form]').forEach((form) => {
        form.addEventListener('submit', () => {
            const submitButton = form.querySelector('button[type="submit"]');
            if (!submitButton) return;
            submitButton.disabled = true;
            submitButton.dataset.original = submitButton.innerHTML;
            submitButton.innerHTML = '<span class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>';
        });
    });
</script>
</body>
</html>
