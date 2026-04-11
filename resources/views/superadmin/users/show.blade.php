<x-layouts.superadmin :title="$pageTitle" :page-title="$pageTitle" :page-subtitle="$pageSubtitle">
    <div class="grid gap-6 xl:grid-cols-[1fr_1fr]">
        <x-superadmin.panel title="Profil overview" subtitle="Role, status va tenant bog'lanishi." icon="user-round">
            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-2xl bg-slate-50 px-4 py-4"><p class="text-xs text-slate-500">Username</p><p class="mt-1 font-semibold text-slate-900">{{ $managedUser->username }}</p></div>
                <div class="rounded-2xl bg-slate-50 px-4 py-4"><p class="text-xs text-slate-500">Status</p><div class="mt-2"><x-superadmin.status-badge :status="$managedUser->status" /></div></div>
                <div class="rounded-2xl bg-slate-50 px-4 py-4"><p class="text-xs text-slate-500">Role</p><p class="mt-1 font-semibold text-slate-900">{{ str($managedUser->role)->headline() }}</p></div>
                <div class="rounded-2xl bg-slate-50 px-4 py-4"><p class="text-xs text-slate-500">Last login</p><p class="mt-1 font-semibold text-slate-900">{{ $managedUser->last_login_at?->format('d.m.Y H:i') ?? 'No login yet' }}</p></div>
                <div class="rounded-2xl bg-slate-50 px-4 py-4 md:col-span-2"><p class="text-xs text-slate-500">Tenant relation</p><p class="mt-1 font-semibold text-slate-900">{{ $managedUser->venueConnection?->venue_name ?? 'Platform-level account' }}</p></div>
            </div>
        </x-superadmin.panel>

        <x-superadmin.panel title="Recent security activity" subtitle="Login va kirish eventlari." icon="shield-check">
            @forelse($managedUser->securityEvents->take(8) as $event)
                <div class="rounded-2xl border border-slate-200 px-4 py-4 {{ !$loop->last ? 'mb-3' : '' }}">
                    <div class="flex items-center justify-between gap-3">
                        <p class="font-semibold text-slate-900">{{ $event->title }}</p>
                        <x-superadmin.status-badge :status="$event->severity" />
                    </div>
                    <p class="mt-1 text-sm text-slate-500">{{ $event->description }}</p>
                    <p class="mt-2 text-xs text-slate-400">{{ optional($event->occurred_at)->diffForHumans() }}</p>
                </div>
            @empty
                <x-superadmin.empty-state icon="shield-check" title="Security event yo'q" description="Ushbu foydalanuvchi uchun hali security stream yozilmagan." />
            @endforelse
        </x-superadmin.panel>
    </div>
</x-layouts.superadmin>
