<x-layouts.superadmin :title="$pageTitle" :page-title="$pageTitle" :page-subtitle="$pageSubtitle">
    <x-superadmin.panel title="Subscription management" subtitle="Plan, status, renewal date va manual override filtrlari." icon="repeat">
        <form method="GET" class="grid gap-3 lg:grid-cols-[.6fr_.6fr_auto]">
            <select name="plan" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                <option value="">Barcha planlar</option>
                @foreach($plans as $plan)
                    <option value="{{ $plan->id }}" @selected((string) $filters['plan'] === (string) $plan->id)>{{ $plan->name }}</option>
                @endforeach
            </select>
            <select name="status" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                <option value="">Barcha statuslar</option>
                @foreach(['active','trial','expired','canceled','past_due'] as $status)
                    <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ str($status)->replace('_', ' ')->headline() }}</option>
                @endforeach
            </select>
            <button class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Filtrlash</button>
        </form>
    </x-superadmin.panel>

    <div class="mt-6 space-y-4">
        @forelse($subscriptions as $subscription)
            <article class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-[0_16px_40px_rgba(15,23,42,0.04)]">
                <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-3">
                            <h3 class="text-lg font-semibold text-slate-950">{{ $subscription->venueConnection?->venue_name ?? 'Biznes biriktirilmagan' }}</h3>
                            <x-superadmin.status-badge :status="$subscription->status" />
                            <x-superadmin.status-badge status="info" :label="'Activity: '.str($subscription->activity_state)->headline()" />
                        </div>
                        <div class="mt-4 grid gap-3 md:grid-cols-4">
                            <div class="rounded-2xl bg-slate-50 px-4 py-3"><p class="text-xs text-slate-500">Plan</p><p class="mt-1 font-semibold text-slate-900">{{ $subscription->plan?->name ?? "Yo'q" }}</p></div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3"><p class="text-xs text-slate-500">Billing cycle</p><p class="mt-1 font-semibold text-slate-900">{{ str($subscription->billing_cycle)->headline() }}</p></div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3"><p class="text-xs text-slate-500">Amount</p><p class="mt-1 font-semibold text-slate-900">{{ number_format((float) $subscription->amount, 0, '.', ' ') }} {{ $subscription->currency }}</p></div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3"><p class="text-xs text-slate-500">Renewal</p><p class="mt-1 font-semibold text-slate-900">{{ $subscription->renews_at?->format('d.m.Y') ?? 'No date' }}</p></div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('superadmin.subscriptions.update', $subscription) }}" class="w-full xl:max-w-lg space-y-3">
                        @csrf
                        @method('PUT')
                        <div class="grid gap-3 sm:grid-cols-2">
                            <select name="subscription_plan_id" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                                <option value="">Plan tanlang</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" @selected($subscription->subscription_plan_id === $plan->id)>{{ $plan->name }}</option>
                                @endforeach
                            </select>
                            <select name="status" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                                @foreach(['active','trial','expired','canceled','past_due'] as $status)
                                    <option value="{{ $status }}" @selected($subscription->status === $status)>{{ str($status)->replace('_', ' ')->headline() }}</option>
                                @endforeach
                            </select>
                            <select name="activity_state" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                                @foreach(['healthy','attention','inactive','risk'] as $item)
                                    <option value="{{ $item }}" @selected($subscription->activity_state === $item)>{{ str($item)->headline() }}</option>
                                @endforeach
                            </select>
                            <input type="date" name="renews_at" value="{{ optional($subscription->renews_at)->format('Y-m-d') }}" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                            <input type="number" name="amount" min="0" step="0.01" value="{{ $subscription->amount }}" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                            <input type="text" name="currency" value="{{ $subscription->currency }}" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        </div>
                        <textarea name="notes" rows="3" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" placeholder="Subscription notes">{{ $subscription->notes }}</textarea>
                        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-600">
                            <input type="checkbox" name="manual_override" value="1" @checked($subscription->manual_override) class="rounded border-slate-300">
                            <span>Manual override faollashtirilsin</span>
                        </label>
                        <button class="w-full rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white">Obunani yangilash</button>
                    </form>
                </div>
            </article>
        @empty
            <x-superadmin.empty-state icon="repeat" title="Obunalar topilmadi" description="Filtr bo'yicha mos subscription yozuvi yo'q." />
        @endforelse
    </div>

    <div class="mt-6">{{ $subscriptions->links() }}</div>
</x-layouts.superadmin>
