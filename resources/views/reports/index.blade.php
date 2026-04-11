<x-app-layout title="Tahlil" pageTitle="Tahlil" pageSubtitle="Moliyaviy, operatsion va xizmat ko'rsatkichlarini bitta premium ish maydonida kuzating.">
    @php
        $tabs = [
            ['key' => 'analytics', 'label' => 'Analitika', 'icon' => 'bar-chart-3'],
            ['key' => 'trends', 'label' => 'Trendlar', 'icon' => 'chart-line'],
            ['key' => 'occupancy', 'label' => 'Bandlik', 'icon' => 'building-2'],
            ['key' => 'services', 'label' => 'Xizmatlar', 'icon' => 'sparkles'],
        ];
        $tabView = match ($activeTab) {
            'trends' => 'reports.partials.trends',
            'occupancy' => 'reports.partials.occupancy',
            'services' => 'reports.partials.services',
            default => 'reports.partials.analytics',
        };
    @endphp

    <div class="space-y-6">
        <x-admin.page-intro eyebrow="Tahlil" title="Moliyaviy va operatsion ko'rsatkichlar" subtitle="Ichki bo'limlar endi faqat yuqoridagi tablar orqali boshqariladi. Har bir tab alohida insight va aniq kontent beradi.">
            <x-slot:actions>
                <div class="flex flex-wrap gap-2">
                    @foreach($tabs as $tab)
                        <a href="{{ route('reports.index', ['tab' => $tab['key']]) }}"
                           class="inline-flex items-center gap-2 rounded-2xl border px-4 py-2 text-sm font-medium transition {{ $activeTab === $tab['key'] ? 'border-primary-300 bg-primary-50 text-primary-800 shadow-sm dark:border-primary-800 dark:bg-primary-950/30 dark:text-primary-200' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300' }}">
                            <i data-lucide="{{ $tab['icon'] }}" class="h-4 w-4"></i>
                            <span>{{ $tab['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </x-slot:actions>
        </x-admin.page-intro>

        @include($tabView)
    </div>
</x-app-layout>
