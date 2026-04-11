<x-layouts.superadmin :title="$pageTitle" :page-title="$pageTitle">
    <div class="space-y-4">
        @foreach($venues as $venue)
            <article class="rounded-3xl border border-slate-200 bg-white p-5">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                    <div>
                        <div class="flex flex-wrap items-center gap-3">
                            <h2 class="text-lg font-semibold text-slate-900">{{ $venue->venue_name }}</h2>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $venue->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($venue->status === 'rejected' ? 'bg-rose-100 text-rose-700' : ($venue->status === 'suspended' ? 'bg-slate-200 text-slate-700' : 'bg-amber-100 text-amber-700')) }}">{{ $venue->status }}</span>
                        </div>
                        <p class="mt-2 text-sm text-slate-500">{{ $venue->owner_name }}  -  {{ $venue->username }}  -  {{ $venue->phone ?: 'telefon yo'q' }}</p>
                        @if($venue->message)<p class="mt-3 text-sm text-slate-600">{{ $venue->message }}</p>@endif
                        <div class="mt-4 grid gap-3 text-sm md:grid-cols-4">
                            <div class="rounded-2xl bg-slate-50 px-4 py-3">Zallar: <strong>{{ $venue->halls_count }}</strong></div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3">Bronlar: <strong>{{ $venue->bookings_count }}</strong></div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3">Revenue: <strong>${{ number_format($venue->revenue_total, 0) }}</strong></div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3">Health: <strong>{{ $venue->health_status }}</strong></div>
                        </div>
                    </div>
                    <div class="w-full max-w-xl">
                        <div class="mb-4 rounded-3xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                            <p class="font-semibold text-slate-900">Maydonlar izohi</p>
                            <div class="mt-3 grid gap-2">
                                <div><strong>Health status:</strong> to'yxonaning texnik yoki ish holati. Masalan: `new`, `active`, `attention`, `offline`.</div>
                                <div><strong>Zallar soni:</strong> ushbu to'yxonadagi umumiy zal yoki banket xonalari soni.</div>
                                <div><strong>Bronlar soni:</strong> tizimda yuritilgan jami bronlar soni.</div>
                                <div><strong>Revenue:</strong> umumiy tushum summasi. Dollarda yoki siz yuritayotgan bazaviy qiymatda kiriting.</div>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('superadmin.venues.update', $venue) }}" class="grid gap-3">
                            @csrf
                            @method('PUT')
                            <div class="grid gap-3 md:grid-cols-2">
                                <div>
                                    <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Status</label>
                                    <select name="status" class="w-full rounded-2xl border border-slate-200 px-4 py-3">@foreach(['pending','approved','rejected','suspended'] as $status)<option value="{{ $status }}" @selected($venue->status === $status)>{{ $status }}</option>@endforeach</select>
                                </div>
                                <div>
                                    <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Health status</label>
                                    <input name="health_status" value="{{ old('health_status', $venue->health_status) }}" placeholder="new / active / attention" class="w-full rounded-2xl border border-slate-200 px-4 py-3">
                                </div>
                                <div>
                                    <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Zallar soni</label>
                                    <input name="halls_count" type="number" min="0" value="{{ old('halls_count', $venue->halls_count) }}" placeholder="masalan 3" class="w-full rounded-2xl border border-slate-200 px-4 py-3">
                                </div>
                                <div>
                                    <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Bronlar soni</label>
                                    <input name="bookings_count" type="number" min="0" value="{{ old('bookings_count', $venue->bookings_count) }}" placeholder="masalan 125" class="w-full rounded-2xl border border-slate-200 px-4 py-3">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Umumiy tushum</label>
                                    <input name="revenue_total" type="number" min="0" step="0.01" value="{{ old('revenue_total', $venue->revenue_total) }}" placeholder="masalan 25000" class="w-full rounded-2xl border border-slate-200 px-4 py-3">
                                </div>
                            </div>
                            <textarea name="approval_notes" rows="3" class="rounded-2xl border border-slate-200 px-4 py-3" placeholder="approval notes">{{ old('approval_notes', $venue->approval_notes) }}</textarea>
                            <div class="flex flex-wrap gap-3">
                                <button type="submit" class="rounded-2xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white">Saqlash / Tasdiqlash</button>
                            </div>
                        </form>
                        @if($venue->pendingUser)
                            <form method="POST" action="{{ route('superadmin.venues.reset-credentials', $venue) }}" class="mt-3">
                                @csrf
                                <button type="submit" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700">Yangi login parol yaratish</button>
                            </form>
                        @endif
                    </div>
                </div>
            </article>
        @endforeach
        <div>{{ $venues->links() }}</div>
    </div>
</x-layouts.superadmin>
