<x-app-layout title="Ta'minotchi" pageTitle="Ta'minotchi kartasi">
    <div class="grid gap-6 xl:grid-cols-3">
        <div class="rounded-3xl bg-white p-6 shadow-soft dark:bg-slate-900 xl:col-span-2">
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900 dark:text-white">{{ $supplier->full_name }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ $supplier->phone }} | {{ $supplier->company_name ?: 'Kompaniya ko\'rsatilmagan' }}</p>
                </div>
                <div class="rounded-2xl bg-amber-50 px-4 py-3 text-right dark:bg-amber-950/30">
                    <p class="text-xs uppercase tracking-[0.2em] text-amber-600 dark:text-amber-300">Joriy balans</p>
                    <p class="mt-1 text-2xl font-semibold text-amber-700 dark:text-amber-200">{{ number_format($supplier->balance, 2) }}</p>
                </div>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-3">
                <div class="rounded-2xl border border-slate-100 p-4 dark:border-slate-800">
                    <p class="text-xs text-slate-500">Boshlang'ich balans</p>
                    <p class="mt-2 text-lg font-semibold">{{ number_format($supplier->opening_balance, 2) }}</p>
                </div>
                <div class="rounded-2xl border border-slate-100 p-4 dark:border-slate-800">
                    <p class="text-xs text-slate-500">Jami kirim</p>
                    <p class="mt-2 text-lg font-semibold">{{ number_format($supplier->purchases_sum_total_amount ?? 0, 2) }}</p>
                </div>
                <div class="rounded-2xl border border-slate-100 p-4 dark:border-slate-800">
                    <p class="text-xs text-slate-500">Jami to'lov</p>
                    <p class="mt-2 text-lg font-semibold">{{ number_format($supplier->payments_sum_amount ?? 0, 2) }}</p>
                </div>
            </div>

            <div class="mt-6 grid gap-6 lg:grid-cols-2">
                <div>
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">So'nggi kirimlar</h3>
                    <div class="mt-3 space-y-3">
                        @forelse($supplier->purchases as $purchase)
                            <div class="rounded-2xl border border-slate-100 px-4 py-3 dark:border-slate-800">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-sm font-medium">{{ optional($purchase->purchase_date)->format('d.m.Y') }}</span>
                                    <span class="text-sm font-semibold">{{ number_format($purchase->total_amount, 2) }}</span>
                                </div>
                                <p class="mt-1 text-xs text-slate-500">{{ $purchase->notes ?: 'Izoh yo\'q' }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">Kirimlar yo'q.</p>
                        @endforelse
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">So'nggi to'lovlar</h3>
                    <div class="mt-3 space-y-3">
                        @forelse($supplier->payments as $payment)
                            <div class="rounded-2xl border border-slate-100 px-4 py-3 dark:border-slate-800">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-sm font-medium">{{ optional($payment->payment_date)->format('d.m.Y') }}</span>
                                    <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-300">{{ number_format($payment->amount, 2) }}</span>
                                </div>
                                <p class="mt-1 text-xs text-slate-500">{{ $payment->notes ?: 'Izoh yo\'q' }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">To'lovlar yo'q.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-3xl bg-white p-6 shadow-soft dark:bg-slate-900">
            <h3 class="text-base font-semibold text-slate-900 dark:text-white">Pul berish</h3>
            <p class="mt-1 text-sm text-slate-500">Ta'minotchiga qilingan to'lovni shu yerdan kiriting.</p>

            <form method="POST" action="{{ route('suppliers.payments.store', $supplier) }}" class="mt-5 space-y-4" data-loading-form>
                @csrf
                <div>
                    <label class="mb-1 block text-sm font-medium">Sana</label>
                    <input type="date" name="payment_date" value="{{ old('payment_date', now()->toDateString()) }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Summa</label>
                    <input type="number" name="amount" step="0.01" min="0.01" value="{{ old('amount') }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Izoh</label>
                    <textarea name="notes" rows="4" class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">{{ old('notes') }}</textarea>
                </div>
                <button type="submit" class="w-full rounded-2xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">To'lovni saqlash</button>
            </form>
        </div>
    </div>
</x-app-layout>
