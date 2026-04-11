<x-layouts.superadmin :title="$pageTitle" :page-title="$pageTitle" :page-subtitle="$pageSubtitle">
    <x-superadmin.panel title="Biznes katalogi" subtitle="Search, status filtr va operational health ko'rinishi." icon="building-2">
        <form method="GET" class="grid gap-3 lg:grid-cols-[1.2fr_.5fr_auto]">
            <input type="text" name="q" value="{{ $filters['search'] }}" placeholder="Biznes, owner, username yoki telefon bo'yicha qidirish" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">
            <select name="status" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">
                <option value="">Barcha statuslar</option>
                @foreach(['pending','under_review','approved','rejected','suspended'] as $status)
                    <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ str($status)->replace('_', ' ')->headline() }}</option>
                @endforeach
            </select>
            <button class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Filtrlash</button>
        </form>
    </x-superadmin.panel>

    <div class="mt-6 space-y-4">
        @forelse($businesses as $business)
            <article class="sa-card-hover rounded-[28px] border border-slate-200/80 bg-white p-5 shadow-[0_16px_40px_rgba(15,23,42,0.04)]">
                <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-3">
                            <h2 class="text-lg font-semibold text-slate-950">{{ $business->venue_name }}</h2>
                            <x-superadmin.status-badge :status="$business->status" />
                            @if($business->health_status)
                                <x-superadmin.status-badge status="info" :label="'Health: '.str($business->health_status)->headline()" />
                            @endif
                        </div>
                        <p class="mt-2 text-sm text-slate-500">{{ $business->owner_name }}  -  {{ $business->phone ?: "Telefon yo'q" }}  -  {{ $business->email ?: $business->username }}</p>
                        <div class="mt-5 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                            <div class="rounded-2xl bg-slate-50 px-4 py-3"><p class="text-xs text-slate-500">Tarif</p><p class="mt-1 font-semibold text-slate-900">{{ $business->latestSubscription?->plan?->name ?? 'Biriktirilmagan' }}</p></div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3"><p class="text-xs text-slate-500">To'lov holati</p><p class="mt-1 font-semibold text-slate-900">{{ $business->latestSubscription?->status ?? 'No subscription' }}</p></div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3"><p class="text-xs text-slate-500">Zallar / bronlar</p><p class="mt-1 font-semibold text-slate-900">{{ $business->halls_count }} / {{ $business->bookings_count }}</p></div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3"><p class="text-xs text-slate-500">Revenue</p><p class="mt-1 font-semibold text-slate-900">{{ number_format((float) $business->revenue_total, 0, '.', ' ') }} UZS</p></div>
                        </div>
                    </div>
                    <div class="w-full xl:max-w-md">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <a href="{{ route('superadmin.businesses.show', $business) }}" class="rounded-2xl border border-slate-200 px-4 py-3 text-center text-sm font-semibold text-slate-700">Batafsil</a>
                            <a href="{{ route('superadmin.approvals.index', ['status' => $business->status]) }}" class="rounded-2xl bg-slate-950 px-4 py-3 text-center text-sm font-semibold text-white">Workflow</a>
                        </div>
                        <p class="mt-3 text-xs text-slate-500">Yaratilgan: {{ $business->created_at?->format('d.m.Y') }}  -  So'nggi faollik: {{ $business->last_seen_at?->diffForHumans() ?? 'No signal' }}</p>
                    </div>
                </div>
            </article>
        @empty
            <x-superadmin.empty-state icon="building-2" title="Bizneslar topilmadi" description="Filtrlar bo'yicha mos yozuv topilmadi. Qidiruvni tozalab qayta urinib ko'ring." />
        @endforelse
    </div>

    <div class="mt-6">{{ $businesses->links() }}</div>
</x-layouts.superadmin>
