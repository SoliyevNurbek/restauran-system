<x-layouts.superadmin :title="$pageTitle" :page-title="$pageTitle" :page-subtitle="$pageSubtitle">
    <x-superadmin.panel title="Approval workflow" subtitle="Pending, review, approve, reject va suspend qarorlari." icon="badge-check">
        <div class="mb-5 flex flex-wrap gap-2">
            @foreach(['pending','under_review','approved','rejected','suspended'] as $item)
                <a href="{{ route('superadmin.approvals.index', ['status' => $item]) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold {{ $status === $item ? 'bg-slate-950 text-white' : 'border border-slate-200 bg-white text-slate-700' }}">
                    {{ str($item)->replace('_', ' ')->headline() }}
                </a>
            @endforeach
        </div>

        @forelse($approvals as $approval)
            <article class="rounded-[24px] border border-slate-200 p-5 {{ !$loop->last ? 'mb-4' : '' }}">
                <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-3">
                            <h3 class="text-lg font-semibold text-slate-950">{{ $approval->venue_name }}</h3>
                            <x-superadmin.status-badge :status="$approval->status" />
                        </div>
                        <p class="mt-2 text-sm text-slate-500">{{ $approval->owner_name }}  -  {{ $approval->phone ?: "Telefon yo'q" }}  -  {{ $approval->username }}</p>
                        <div class="mt-4 grid gap-3 md:grid-cols-3">
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">Created <strong class="text-slate-950">{{ $approval->created_at?->format('d.m.Y H:i') }}</strong></div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">Current plan <strong class="text-slate-950">{{ $approval->latestSubscription?->plan?->name ?? 'Trial setup' }}</strong></div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">Last review <strong class="text-slate-950">{{ $approval->reviewed_at?->diffForHumans() ?? "Yo'q" }}</strong></div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('superadmin.approvals.update', $approval) }}" class="w-full xl:max-w-md space-y-3">
                        @csrf
                        @method('PUT')
                        <select name="status" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                            @foreach(['pending','under_review','approved','rejected','suspended'] as $item)
                                <option value="{{ $item }}" @selected($approval->status === $item)>{{ str($item)->replace('_', ' ')->headline() }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="review_reason" value="{{ $approval->review_reason }}" placeholder="Qaror sababi" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        <textarea name="approval_notes" rows="3" placeholder="Internal moderation notes" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">{{ $approval->approval_notes }}</textarea>
                        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-600">
                            <input type="checkbox" name="send_telegram" value="1" class="rounded border-slate-300">
                            <span>Muhim qaror uchun Telegram alert</span>
                        </label>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <a href="{{ route('superadmin.businesses.show', $approval) }}" class="rounded-2xl border border-slate-200 px-4 py-3 text-center text-sm font-semibold text-slate-700">Ko'rish</a>
                            <button class="rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white">Qarorni saqlash</button>
                        </div>
                    </form>
                </div>
            </article>
        @empty
            <x-superadmin.empty-state icon="badge-check" title="Pending approval yo'q" description="Moderation navbatida yangi biznes bo'lmasa bu bo'lim bo'sh ko'rinadi." />
        @endforelse
    </x-superadmin.panel>

    <div class="mt-6">{{ $approvals->links() }}</div>
</x-layouts.superadmin>
