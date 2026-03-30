<x-app-layout title="Bosh sahifa" pageTitle="Bosh sahifa">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card title="Jami buyurtmalar" :value="$stats['totalOrders']" icon="shopping-bag" />
        <x-stat-card title="Jami tushum" :value="'$'.number_format($stats['totalRevenue'], 2)" icon="wallet" />
        <x-stat-card title="Band stollar" :value="$stats['activeTables']" icon="armchair" />
        <x-stat-card title="Kunlik savdo" :value="'$'.number_format($stats['dailySales'], 2)" icon="calendar" />
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="rounded-2xl bg-white p-5 shadow-soft dark:bg-slate-900 xl:col-span-2">
            <h3 class="mb-4 text-sm font-semibold">So'nggi 7 kun savdosi</h3>
            <canvas id="salesChart" height="130"></canvas>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-soft dark:bg-slate-900">
            <h3 class="mb-4 text-sm font-semibold">So'nggi buyurtmalar</h3>
            <div class="space-y-3">
                @forelse($recentOrders as $order)
                    <div class="rounded-xl border border-slate-100 p-3 dark:border-slate-800">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-medium">{{ $order->order_number }}</p>
                            <x-status-badge :status="$order->status" />
                        </div>
                        <p class="mt-1 text-xs text-slate-500">{{ $order->customer?->name ?? 'Tashrif buyuruvchi' }} • ${{ number_format($order->total, 2) }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Buyurtmalar hali yo'q.</p>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('salesChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Savdo',
                        data: @json($chartValues),
                        borderColor: '#4f9f6c',
                        backgroundColor: 'rgba(79, 159, 108, 0.18)',
                        fill: true,
                        tension: 0.35,
                    }]
                },
                options: {
                    plugins: {legend: {display: false}},
                    scales: {y: {beginAtZero: true}}
                }
            });
        }
    </script>
</x-app-layout>
