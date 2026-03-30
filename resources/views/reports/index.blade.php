<x-app-layout title="Hisobotlar" pageTitle="Hisobotlar">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card title="Jami kirim" :value="number_format($totalPurchases, 2)" icon="package-plus" />
        <x-stat-card title="Jami xarajat" :value="number_format($totalExpenses, 2)" icon="wallet-cards" />
        <x-stat-card title="Ta'minotchilar" :value="$totalSuppliers" icon="truck" />
        <x-stat-card title="Kam qolgan mahsulot" :value="$lowStockCount" icon="triangle-alert" />
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="rounded-3xl bg-white p-5 shadow-soft dark:bg-slate-900 xl:col-span-2">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">So'nggi 6 oy dinamikasi</h3>
            <canvas id="reportChart" height="120" class="mt-4"></canvas>
        </div>

        <div class="rounded-3xl bg-white p-5 shadow-soft dark:bg-slate-900">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Kategoriya bo'yicha xarajat</h3>
            <div class="mt-4 space-y-3">
                @forelse($expenseByCategory as $category)
                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-100 px-4 py-3 text-sm dark:border-slate-800">
                        <span>{{ $category->name }}</span>
                        <span class="font-semibold">{{ number_format($category->total, 2) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Kategoriyalar bo'yicha ma'lumot yo'q.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2 xl:grid-cols-3">
        <div class="rounded-3xl bg-white p-5 shadow-soft dark:bg-slate-900">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Ko'p olinadigan mahsulotlar</h3>
            <div class="mt-4 space-y-3">
                @foreach($topProducts as $product)
                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-100 px-4 py-3 text-sm dark:border-slate-800">
                        <span>{{ $product->name }}</span>
                        <span class="font-semibold">{{ number_format($product->quantity, 3) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-3xl bg-white p-5 shadow-soft dark:bg-slate-900 xl:col-span-2">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Ta'minotchi kesimidagi qarzdorlik</h3>
            <div class="mt-4 grid gap-3 md:grid-cols-2">
                @foreach($topSuppliers as $supplier)
                    <div class="rounded-2xl border border-slate-100 px-4 py-3 dark:border-slate-800">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-sm font-medium">{{ $supplier->full_name }}</span>
                            <span class="text-sm font-semibold">{{ number_format($supplier->balance, 2) }}</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">Kirim: {{ number_format($supplier->purchases_sum_total_amount ?? 0, 2) }} | To'lov: {{ number_format($supplier->payments_sum_amount ?? 0, 2) }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        const reportChart = document.getElementById('reportChart');
        if (reportChart) {
            new Chart(reportChart, {
                type: 'bar',
                data: {
                    labels: @json($labels),
                    datasets: [
                        {label: 'Kirim', data: @json($purchaseValues), backgroundColor: 'rgba(62, 133, 80, 0.85)', borderRadius: 10},
                        {label: 'Xarajat', data: @json($expenseValues), backgroundColor: 'rgba(220, 38, 38, 0.78)', borderRadius: 10}
                    ]
                },
                options: {responsive: true, plugins: {legend: {position: 'bottom'}}, scales: {y: {beginAtZero: true}}}
            });
        }
    </script>
</x-app-layout>
