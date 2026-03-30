<x-app-layout title="Bosh sahifa" pageTitle="Bosh sahifa">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card title="Bugungi kirim" :value="number_format($stats['todayPurchases'], 2)" icon="package-plus" />
        <x-stat-card title="Oylik kirim" :value="number_format($stats['monthPurchases'], 2)" icon="wallet" />
        <x-stat-card title="Oylik xarajat" :value="number_format($stats['monthExpenses'], 2)" icon="receipt-text" />
        <x-stat-card title="Ta'minotchi balansi" :value="number_format($stats['supplierDebt'], 2)" icon="hand-coins" />
        <x-stat-card title="Mahsulotlar" :value="$stats['productCount']" icon="boxes" />
        <x-stat-card title="Kam qolgan mahsulot" :value="$stats['lowStockCount']" icon="triangle-alert" />
        <x-stat-card title="Ta'minotchilar" :value="$stats['supplierCount']" icon="users" />
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="rounded-3xl bg-white p-5 shadow-soft dark:bg-slate-900 xl:col-span-2">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">So'nggi 7 kun oqimi</h3>
            <p class="mt-1 text-xs text-slate-500">Kirim va xarajatlarni bir joyda kuzatish</p>
            <canvas id="flowChart" height="120" class="mt-4"></canvas>
        </div>

        <div class="rounded-3xl bg-white p-5 shadow-soft dark:bg-slate-900">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Kam qolgan mahsulotlar</h3>
            <div class="mt-4 space-y-3">
                @forelse($lowStockProducts as $product)
                    <div class="rounded-2xl border border-slate-100 px-4 py-3 dark:border-slate-800">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-medium">{{ $product->name }}</p>
                            <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700 dark:bg-amber-950/40 dark:text-amber-300">Kam qoldi</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">Qoldiq: {{ number_format($product->current_stock, 3) }} {{ $product->unit }} | Minimum: {{ number_format($product->minimum_stock, 3) }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Barcha mahsulotlar yetarli.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="rounded-3xl bg-white p-5 shadow-soft dark:bg-slate-900">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">So'nggi kirimlar</h3>
            <div class="mt-4 space-y-3">
                @forelse($latestPurchases as $purchase)
                    <div class="rounded-2xl border border-slate-100 px-4 py-3 dark:border-slate-800">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-medium">{{ $purchase->supplier?->full_name }}</p>
                            <span class="text-sm font-semibold">{{ number_format($purchase->total_amount, 2) }}</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">{{ optional($purchase->purchase_date)->format('d.m.Y') }} | {{ $purchase->items->count() }} ta pozitsiya</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Kirimlar hali kiritilmagan.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-3xl bg-white p-5 shadow-soft dark:bg-slate-900">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">So'nggi xarajatlar</h3>
            <div class="mt-4 space-y-3">
                @forelse($latestExpenses as $expense)
                    <div class="rounded-2xl border border-slate-100 px-4 py-3 dark:border-slate-800">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-medium">{{ $expense->title }}</p>
                            <span class="text-sm font-semibold">{{ number_format($expense->amount, 2) }}</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">{{ $expense->category?->name ?? 'Kategoriya yo\'q' }} | {{ optional($expense->expense_date)->format('d.m.Y') }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Xarajatlar hali kiritilmagan.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-3xl bg-white p-5 shadow-soft dark:bg-slate-900">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Faol ta'minotchilar</h3>
            <div class="mt-4 space-y-3">
                @forelse($topSuppliers as $supplier)
                    <div class="rounded-2xl border border-slate-100 px-4 py-3 dark:border-slate-800">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-medium">{{ $supplier->full_name }}</p>
                            <span class="text-sm font-semibold">{{ number_format($supplier->balance, 2) }}</span>
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
                options: {plugins: {legend: {position: 'bottom'}}, scales: {y: {beginAtZero: true}}}
            });
        }
    </script>
</x-app-layout>
