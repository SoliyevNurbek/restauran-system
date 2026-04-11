<x-app-layout title="Sozlamalar" pageTitle="Sozlamalar" pageSubtitle="Biznes konfiguratsiyasi endi faqat yuqoridagi tablar orqali tartibli boshqariladi.">
    @php
        $tabs = [
            ['key' => 'business', 'label' => 'Biznes', 'icon' => 'building-2'],
            ['key' => 'notifications', 'label' => 'Bildirishnoma', 'icon' => 'bell'],
            ['key' => 'integrations', 'label' => 'Integratsiya', 'icon' => 'plug'],
            ['key' => 'security', 'label' => 'Xavfsizlik', 'icon' => 'shield-check'],
        ];
        $tabView = match ($activeSection) {
            'notifications' => 'settings.partials.notifications',
            'integrations' => 'settings.partials.integrations',
            'security' => 'settings.partials.security',
            default => 'settings.partials.business',
        };
    @endphp

    <div class="space-y-6">
        <x-admin.page-intro eyebrow="Tenant panel" title="Biznes sozlamalari" subtitle="Har bir bo'lim faqat tab orqali ochiladi va faqat o'ziga tegishli sozlamalarni ko'rsatadi.">
            <x-slot:actions>
                <div class="flex flex-wrap gap-2">
                    @foreach($tabs as $tab)
                        <a href="{{ route('settings.edit', ['section' => $tab['key']]) }}"
                           class="inline-flex items-center gap-2 rounded-2xl border px-4 py-2 text-sm font-medium transition {{ $activeSection === $tab['key'] ? 'border-primary-300 bg-primary-50 text-primary-800 shadow-sm dark:border-primary-800 dark:bg-primary-950/30 dark:text-primary-200' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300' }}">
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
