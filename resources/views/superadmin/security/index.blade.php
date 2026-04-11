<x-layouts.superadmin :title="$pageTitle" :page-title="$pageTitle" :page-subtitle="$pageSubtitle">
    <div class="grid gap-6 xl:grid-cols-[.95fr_1.05fr]">
        <x-superadmin.panel title="Recent login activity" subtitle="Platforma darajasida oxirgi kirishlar." icon="log-in">
            @forelse($recentLogins as $user)
                <div class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 px-4 py-4 {{ !$loop->last ? 'mb-3' : '' }}">
                    <div>
                        <p class="font-semibold text-slate-900">{{ $user->name }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $user->username }}  -  {{ $user->last_login_ip ?: "IP yo'q" }}</p>
                    </div>
                    <p class="text-sm text-slate-500">{{ $user->last_login_at?->diffForHumans() }}</p>
                </div>
            @empty
                <x-superadmin.empty-state icon="log-in" title="Login activity topilmadi" description="Foydalanuvchilar tizimga kirgach recent login activity shu blokda ko'rinadi." />
            @endforelse
        </x-superadmin.panel>

        <x-superadmin.panel title="Security events" subtitle="Failed login, suspicious activity va sezgir hodisalar." icon="shield-alert">
            @forelse($events as $event)
                <div class="rounded-[24px] border border-slate-200 p-5 {{ !$loop->last ? 'mb-4' : '' }}">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $event->title }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $event->user?->name ?? $event->venueConnection?->venue_name ?? 'System event' }}</p>
                        </div>
                        <x-superadmin.status-badge :status="$event->severity" />
                    </div>
                    <p class="mt-3 text-sm text-slate-600">{{ $event->description }}</p>
                    <p class="mt-2 text-xs text-slate-400">{{ optional($event->occurred_at)->format('d.m.Y H:i') }}  -  {{ $event->ip ?: "IP yo'q" }}</p>
                </div>
            @empty
                <x-superadmin.empty-state icon="shield-check" title="Security events yo'q" description="Shubhali kirishlar yoki xavfsizlik signalari paydo bo'lganda shu bo'limda ko'rinadi." />
            @endforelse
        </x-superadmin.panel>
    </div>

    <div class="mt-6">{{ $events->links() }}</div>
</x-layouts.superadmin>
