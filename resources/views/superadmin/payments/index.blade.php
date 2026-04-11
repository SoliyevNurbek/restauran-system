<x-layouts.superadmin :title="$pageTitle" :page-title="$pageTitle" :page-subtitle="$pageSubtitle">
    <div class="grid gap-4 md:grid-cols-3">
        <x-superadmin.stat-card title="Paid volume" :value="number_format($totals['paid'], 0, '.', ' ').' UZS'" icon="banknote" tone="green" />
        <x-superadmin.stat-card title="Pending volume" :value="number_format($totals['pending'], 0, '.', ' ').' UZS'" icon="wallet" tone="amber" />
        <x-superadmin.stat-card title="Failed count" :value="number_format($totals['failed_count'])" icon="x-circle" tone="red" />
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.4fr_.9fr]">
        <x-superadmin.panel title="Manual payment requests" subtitle="Telegram orqali kelgan чеклар va billing review jarayoni." icon="credit-card">
            <form method="GET" class="grid gap-3 lg:grid-cols-[1.2fr_.8fr_.8fr_.8fr_.8fr_.8fr_auto]">
                <input type="text" name="q" value="{{ $filters['search'] }}" placeholder="Invoice, reference yoki notes bo'yicha qidirish" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                <select name="status" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    <option value="">Barcha statuslar</option>
                    @foreach(['paid','pending','payment_details_sent','awaiting_proof','under_review','failed','rejected','refunded','canceled'] as $status)
                        <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ str($status)->headline() }}</option>
                    @endforeach
                </select>
                <select name="method" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    <option value="">Barcha usullar</option>
                    @foreach($methods as $method)
                        <option value="{{ $method->id }}" @selected((string) $filters['method'] === (string) $method->id)>{{ $method->label }}</option>
                    @endforeach
                </select>
                <select name="plan" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    <option value="">Barcha tariflar</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}" @selected((string) $filters['plan'] === (string) $plan->id)>{{ $plan->name }}</option>
                    @endforeach
                </select>
                <select name="provider" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    <option value="">Barcha providerlar</option>
                    @foreach(['manual_telegram','manual','click','payme'] as $provider)
                        <option value="{{ $provider }}" @selected($filters['provider'] === $provider)>{{ str($provider)->replace('_', ' ')->headline() }}</option>
                    @endforeach
                </select>
                <input type="date" name="date" value="{{ $filters['date'] }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                <input type="text" name="business" value="{{ $filters['business'] }}" placeholder="Biznes" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                <button class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Filtrlash</button>
            </form>

            <div class="mt-5 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-xs uppercase tracking-[0.2em] text-slate-400">
                        <tr>
                            <th class="px-3 py-3">Invoice</th>
                            <th class="px-3 py-3">Biznes</th>
                            <th class="px-3 py-3">Tarif</th>
                            <th class="px-3 py-3">Method</th>
                            <th class="px-3 py-3">Amount</th>
                            <th class="px-3 py-3">Status</th>
                            <th class="px-3 py-3">Proof</th>
                            <th class="px-3 py-3">Vaqt</th>
                            <th class="px-3 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr class="border-t border-slate-200">
                                <td class="px-3 py-4">
                                    <div class="font-semibold text-slate-900">{{ $payment->invoice_number ?: 'No invoice' }}</div>
                                    <div class="text-xs text-slate-500">{{ $payment->transaction_reference ?: "Reference yo'q" }}</div>
                                </td>
                                <td class="px-3 py-4 text-slate-600">{{ $payment->venueConnection?->venue_name ?? 'N/A' }}</td>
                                <td class="px-3 py-4 text-slate-600">{{ $payment->plan?->name ?? $payment->subscription?->plan?->name ?? 'N/A' }}</td>
                                <td class="px-3 py-4 text-slate-600">
                                    <div>{{ $payment->paymentMethod?->label ?? 'N/A' }}</div>
                                    <div class="text-xs text-slate-400">{{ str($payment->provider)->replace('_', ' ')->headline() }}</div>
                                </td>
                                <td class="px-3 py-4 font-semibold text-slate-900">{{ number_format((float) $payment->amount, 0, '.', ' ') }} {{ $payment->currency }}</td>
                                <td class="px-3 py-4"><x-superadmin.status-badge :status="$payment->status" /></td>
                                <td class="px-3 py-4 text-slate-600">
                                    @if($payment->proof_file_path)
                                        <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">Bor</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600">Yo‘q</span>
                                    @endif
                                </td>
                                <td class="px-3 py-4 text-slate-600">{{ ($payment->proof_received_at ?? $payment->created_at)?->format('d.m.Y H:i') }}</td>
                                <td class="px-3 py-4 text-right"><a href="{{ route('superadmin.payments.show', $payment) }}" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700">Ko'rish</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-0 py-6"><x-superadmin.empty-state icon="wallet-cards" title="To'lovlar hali yo'q" description="Birinchi billing yozuvi yaratilgach premium payment table shu yerda to'ladi." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-superadmin.panel>

        <x-superadmin.panel title="Payment methods" subtitle="Gateway va manual methodlar konfiguratsiyasi." icon="wallet">
            @foreach($methods as $method)
                <form method="POST" action="{{ route('superadmin.payment-methods.update', $method) }}" class="rounded-[24px] border border-slate-200 p-4 {{ !$loop->last ? 'mb-4' : '' }}">
                    @csrf
                    @method('PUT')
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="font-semibold text-slate-900">{{ $method->code }}</h3>
                        <x-superadmin.status-badge :status="$method->is_enabled ? 'active' : 'canceled'" :label="$method->is_enabled ? 'Enabled' : 'Disabled'" />
                    </div>
                    <div class="mt-3 grid gap-3">
                        <input type="text" name="label" value="{{ $method->label }}" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <select name="type" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                                @foreach(['manual','online','gateway'] as $type)
                                    <option value="{{ $type }}" @selected($method->type === $type)>{{ str($type)->headline() }}</option>
                                @endforeach
                            </select>
                            <input type="number" name="display_order" min="1" value="{{ $method->display_order }}" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        </div>
                        <input type="text" name="config_placeholder" value="{{ data_get($method->config, 'placeholder') }}" placeholder="Internal config placeholder" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        <textarea name="notes" rows="2" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm">{{ $method->notes }}</textarea>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <label class="flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-600"><input type="checkbox" name="is_enabled" value="1" @checked($method->is_enabled) class="rounded border-slate-300"> <span>Faol</span></label>
                            <label class="flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-600"><input type="checkbox" name="proof_required" value="1" @checked($method->proof_required) class="rounded border-slate-300"> <span>Proof required</span></label>
                        </div>
                        <button class="rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white">Saqlash</button>
                    </div>
                </form>
            @endforeach
        </x-superadmin.panel>
    </div>

    <div class="mt-6">{{ $payments->links() }}</div>
</x-layouts.superadmin>
