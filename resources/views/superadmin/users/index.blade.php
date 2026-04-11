<x-layouts.superadmin :title="$pageTitle" :page-title="$pageTitle" :page-subtitle="$pageSubtitle">
    <x-superadmin.panel title="User directory" subtitle="Platforma darajasidagi barcha foydalanuvchilar." icon="users">
        @forelse($users as $user)
            <div class="flex flex-col gap-5 rounded-[24px] border border-slate-200 p-5 {{ !$loop->last ? 'mb-4' : '' }} lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-3">
                        <h3 class="text-lg font-semibold text-slate-950">{{ $user->name }}</h3>
                        <x-superadmin.status-badge :status="$user->status" />
                        <x-superadmin.status-badge status="info" :label="str($user->role)->headline()" />
                    </div>
                    <p class="mt-2 text-sm text-slate-500">{{ $user->username }}  -  {{ $user->email ?: "Email yo'q" }}</p>
                    <div class="mt-4 grid gap-3 md:grid-cols-3">
                        <div class="rounded-2xl bg-slate-50 px-4 py-3"><p class="text-xs text-slate-500">Venue</p><p class="mt-1 font-semibold text-slate-900">{{ $user->venueConnection?->venue_name ?? 'Platform-level' }}</p></div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3"><p class="text-xs text-slate-500">Subscription</p><p class="mt-1 font-semibold text-slate-900">{{ $user->venueConnection?->latestSubscription?->plan?->name ?? "Yo'q" }}</p></div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3"><p class="text-xs text-slate-500">Last login</p><p class="mt-1 font-semibold text-slate-900">{{ $user->last_login_at?->diffForHumans() ?? 'Hali kirmagan' }}</p></div>
                    </div>
                </div>
                <div class="w-full lg:max-w-md">
                    <form method="POST" action="{{ route('superadmin.users.update', $user) }}" class="grid gap-3 sm:grid-cols-2">
                        @csrf
                        @method('PUT')
                        <select name="role" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                            @foreach(['superadmin','admin','manager','staff'] as $role)
                                <option value="{{ $role }}" @selected($user->role === $role)>{{ str($role)->headline() }}</option>
                            @endforeach
                        </select>
                        <select name="status" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                            @foreach(['active','pending','inactive','suspended'] as $state)
                                <option value="{{ $state }}" @selected($user->status === $state)>{{ str($state)->headline() }}</option>
                            @endforeach
                        </select>
                        <label class="sm:col-span-2 flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-600">
                            <input type="checkbox" name="reset_password" value="1" class="rounded border-slate-300">
                            <span>Xavfsiz reset parol yaratish</span>
                        </label>
                        <a href="{{ route('superadmin.users.show', $user) }}" class="rounded-2xl border border-slate-200 px-4 py-3 text-center text-sm font-semibold text-slate-700">Profil</a>
                        <button class="rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white">Saqlash</button>
                    </form>
                </div>
            </div>
        @empty
            <x-superadmin.empty-state icon="users" title="Foydalanuvchilar topilmadi" description="Platformadagi userlar shu yerda ko'rinadi. Hozircha ma'lumot yo'q." />
        @endforelse
    </x-superadmin.panel>

    <div class="mt-6">{{ $users->links() }}</div>
</x-layouts.superadmin>
