<x-app-layout title="To'lovlar" pageTitle="To'lovlar" pageSubtitle="SaaS billing bo‘yicha invoice, provider va status tarixini ko‘ring.">
    <div class="space-y-6">
        <x-admin.page-intro eyebrow="SaaS Billing" title="To‘lovlar" subtitle="Obuna, renewal va upgrade to‘lovlari bo‘yicha aniq tarix va statuslar.">
            <x-slot:actions>
                <a href="{{ route('billing.plans.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    Yangi to‘lov boshlash
                </a>
            </x-slot:actions>
        </x-admin.page-intro>

        <x-admin.filter-shell>
            <form method="GET" class="grid gap-3 md:grid-cols-[1fr_auto]">
                <select name="status" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slate-400 dark:border-slate-700 dark:bg-slate-950">
                    <option value="">Barcha statuslar</option>
                    @foreach(['paid' => 'Paid', 'pending' => 'Pending', 'failed' => 'Failed', 'canceled' => 'Canceled', 'refunded' => 'Refunded'] as $value => $label)
                        <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <button class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white dark:bg-white dark:text-slate-950">Filtrlash</button>
                    <a href="{{ route('billing.payments.index') }}" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 dark:border-slate-700 dark:text-slate-300">Tozalash</a>
                </div>
            </form>
        </x-admin.filter-shell>

        @if($payments->count())
            <div class="overflow-hidden rounded-[30px] border border-slate-200/80 bg-white shadow-soft dark:border-slate-800 dark:bg-slate-900">
                <div class="mobile-fit-table overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50/90 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400 dark:bg-slate-950/70">
                            <tr>
                                <th class="px-5 py-4">Sana</th>
                                <th class="px-5 py-4">Summa</th>
                                <th class="px-5 py-4">Status</th>
                                <th class="px-5 py-4">Usul</th>
                                <th class="px-5 py-4">Provider</th>
                                <th class="px-5 py-4">Tarif</th>
                                <th class="px-5 py-4">Invoice / tranzaksiya</th>
                                <th class="px-5 py-4">Izoh</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($payments as $payment)
                                <tr @class(['transition hover:bg-slate-50/70 dark:hover:bg-slate-950/40', 'bg-primary-50/40 dark:bg-primary-950/10' => (string) $payment->id === $filters['highlight']])>
                                    <td class="px-5 py-4">{{ optional($payment->paid_at ?? $payment->created_at)->format('d.m.Y H:i') }}</td>
                                    <td class="px-5 py-4 font-semibold text-slate-900 dark:text-white">{{ number_format($payment->amount, 0, '.', ' ') }} {{ $payment->currency }}</td>
                                    <td class="px-5 py-4">
                                        <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $payment->status === 'paid' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-300' : (in_array($payment->status, ['failed', 'canceled', 'refunded'], true) ? 'bg-rose-100 text-rose-700 dark:bg-rose-950/30 dark:text-rose-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-950/30 dark:text-amber-300') }}">{{ $payment->display_status }}</span>
                                    </td>
                                    <td class="px-5 py-4">{{ $payment->method ?: $payment->paymentMethod?->label ?: '—' }}</td>
                                    <td class="px-5 py-4">{{ strtoupper($payment->provider) }}</td>
                                    <td class="px-5 py-4">{{ $payment->plan?->name ?? $payment->subscription?->plan?->name ?? '—' }}</td>
                                    <td class="px-5 py-4">
                                        <p class="font-medium text-slate-900 dark:text-white">{{ $payment->invoice_number ?? '—' }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $payment->transaction_reference ?: $payment->provider_payment_id ?: '—' }}</p>
                                    </td>
                                    <td class="px-5 py-4 text-slate-500">{{ $payment->description ?: $payment->notes ?: '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <x-admin.empty-state icon="credit-card" title="Billing to‘lovlari topilmadi" text="Tarif tanlangandan keyin billing yozuvlari shu yerda chiqadi." action-href="{{ route('billing.plans.index') }}" action-label="Tarif tanlash" />
        @endif

        <div>{{ $payments->links() }}</div>
    </div>
</x-app-layout>
