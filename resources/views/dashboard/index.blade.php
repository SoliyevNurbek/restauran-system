<x-app-layout title="Bosh sahifa" pageTitle="Bosh sahifa">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card title="Bugungi kirim" :value="number_format($stats['todayPurchases'], 2, '.', ' ')" suffix="so'm" icon="package-plus" />
        <x-stat-card title="Oylik kirim" :value="number_format($stats['monthPurchases'], 2, '.', ' ')" suffix="so'm" icon="wallet" />
        <x-stat-card title="Oylik xarajat" :value="number_format($stats['monthExpenses'], 2, '.', ' ')" suffix="so'm" icon="receipt-text" />
        <x-stat-card title="Ta'minotchi balansi" :value="number_format($stats['supplierDebt'], 2, '.', ' ')" suffix="so'm" icon="hand-coins" />
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.65fr_1fr]">
        <div class="rounded-[2rem] border border-slate-200/70 bg-white p-6 shadow-soft dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Analitika</p>
                    <h3 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">So'nggi 7 kun oqimi</h3>
                    <p class="mt-1 text-sm text-slate-500">Kirim va xarajatlarning qisqa muddatli trendi</p>
                </div>
                <div class="grid grid-cols-2 gap-3 text-xs text-slate-500">
                    <div class="rounded-2xl bg-emerald-50 px-3 py-2 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-300">Kirim: {{ collect($purchaseValues)->sum() > 0 ? 'faol' : 'yo\'q' }}</div>
                    <div class="rounded-2xl bg-rose-50 px-3 py-2 text-rose-700 dark:bg-rose-950/30 dark:text-rose-300">Xarajat: {{ collect($expenseValues)->sum() > 0 ? 'faol' : 'yo\'q' }}</div>
                </div>
            </div>
            <canvas id="flowChart" height="110" class="mt-6"></canvas>
        </div>

        <div class="rounded-[2rem] border border-slate-200/70 bg-white p-6 shadow-soft dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Ombor ogohlantirish</p>
                    <h3 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">Kam qolgan mahsulotlar</h3>
                </div>
                <a href="{{ route('dashboard.low-stock-word') }}" class="inline-flex items-center gap-2 rounded-2xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                    <i data-lucide="file-text" class="h-4 w-4"></i>
                    Yuklab olish
                </a>
            </div>

            <div class="mt-4 space-y-3">
                @forelse($lowStockProducts as $product)
                    <div class="rounded-3xl border border-slate-200/70 p-4 dark:border-slate-800">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $product->name }}</p>
                                <p class="mt-1 text-xs text-slate-500">Minimal limit: {{ number_format($product->minimum_stock, 3) }} {{ $product->unit }}</p>
                            </div>
                            <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700 dark:bg-amber-950/40 dark:text-amber-300">Kam qoldi</span>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <div class="rounded-2xl bg-slate-50 px-3 py-3 dark:bg-slate-800/80">
                                <p class="text-[11px] uppercase tracking-[0.2em] text-slate-400">Qoldiq</p>
                                <p class="mt-1 text-lg font-semibold text-slate-900 dark:text-white">{{ number_format($product->current_stock, 3) }} <span class="text-xs font-medium text-slate-400">{{ $product->unit }}</span></p>
                            </div>
                            <div class="rounded-2xl bg-emerald-50 px-3 py-3 dark:bg-emerald-950/20">
                                <p class="text-[11px] uppercase tracking-[0.2em] text-emerald-600 dark:text-emerald-300">Olib kelish kerak</p>
                                <p class="mt-1 text-lg font-semibold text-emerald-700 dark:text-emerald-300">{{ number_format($product->restock_amount, 3) }} <span class="text-xs font-medium text-emerald-500">{{ $product->unit }}</span></p>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="rounded-3xl border border-dashed border-emerald-200 bg-emerald-50 px-4 py-5 text-sm text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-950/20 dark:text-emerald-300">Barcha mahsulotlar yetarli.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="rounded-[2rem] border border-slate-200/70 bg-white p-5 shadow-soft dark:border-slate-800 dark:bg-slate-900">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">So'nggi kirimlar</h3>
            <div class="mt-4 space-y-3">
                @forelse($latestPurchases as $purchase)
                    <div class="rounded-2xl border border-slate-100 px-4 py-3 dark:border-slate-800">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-medium">{{ $purchase->supplier?->full_name }}</p>
                            <x-money :value="$purchase->total_amount" class="text-sm font-semibold" />
                        </div>
                        <p class="mt-1 text-xs text-slate-500">{{ optional($purchase->purchase_date)->format('d.m.Y') }} | {{ $purchase->items->count() }} ta pozitsiya</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Kirimlar hali kiritilmagan.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-[2rem] border border-slate-200/70 bg-white p-5 shadow-soft dark:border-slate-800 dark:bg-slate-900">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">So'nggi xarajatlar</h3>
            <div class="mt-4 space-y-3">
                @forelse($latestExpenses as $expense)
                    <div class="rounded-2xl border border-slate-100 px-4 py-3 dark:border-slate-800">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-medium">{{ $expense->title }}</p>
                            <x-money :value="$expense->amount" class="text-sm font-semibold" />
                        </div>
                        <p class="mt-1 text-xs text-slate-500">{{ $expense->category?->name ?? 'Kategoriya yo\'q' }} | {{ optional($expense->expense_date)->format('d.m.Y') }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Xarajatlar hali kiritilmagan.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-[2rem] border border-slate-200/70 bg-white p-5 shadow-soft dark:border-slate-800 dark:bg-slate-900">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Faol ta'minotchilar</h3>
            <div class="mt-4 space-y-3">
                @forelse($topSuppliers as $supplier)
                    <div class="rounded-2xl border border-slate-100 px-4 py-3 dark:border-slate-800">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-medium">{{ $supplier->full_name }}</p>
                            <x-money :value="$supplier->balance" class="text-sm font-semibold" />
                        </div>
                        <p class="mt-1 text-xs text-slate-500">{{ $supplier->company_name ?: 'Kompaniya ko\'rsatilmagan' }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Ta'minotchi ma'lumotlari yo'q.</p>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        const chart = document.getElementById('flowChart');
        if (chart) {
            new Chart(chart, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [
                        {label: 'Kirim', data: @json($purchaseValues), borderColor: '#3e8550', backgroundColor: 'rgba(62, 133, 80, 0.14)', fill: true, tension: 0.35},
                        {label: 'Xarajat', data: @json($expenseValues), borderColor: '#dc2626', backgroundColor: 'rgba(220, 38, 38, 0.08)', fill: true, tension: 0.35}
                    ]
                },
                options: {
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 8,
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(148, 163, 184, 0.12)',
                            }
                        }
                    }
                }
            });
        }
    </script>
</x-app-layout>
