<x-app-layout title="Hisobotlar" pageTitle="Savdo hisobotlari">
    <div class="grid gap-4 md:grid-cols-2">
        <x-stat-card title="To'langan buyurtmalar" :value="$paidOrderCount" icon="receipt" />
        <x-stat-card title="Tushum" :value="'$'.number_format($totalPaidRevenue, 2)" icon="line-chart" />
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl bg-white p-5 shadow-soft dark:bg-slate-900">
            <h3 class="mb-4 text-sm font-semibold">Kunlik tushum (so'nggi 7 kun)</h3>
            <canvas id="dailyChart" height="140"></canvas>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-soft dark:bg-slate-900">
            <h3 class="mb-4 text-sm font-semibold">Oylik tushum (joriy oy)</h3>
            <canvas id="monthlyChart" height="140"></canvas>
        </div>
    </div>

    <script>
        const makeBar = (id, labels, values, color) => {
            const el = document.getElementById(id);
            if (!el) return;
            new Chart(el, {
                type: 'bar',
                data: {labels, datasets: [{data: values, backgroundColor: color, borderRadius: 8}]},
                options: {plugins:{legend:{display:false}}, scales: {y: {beginAtZero: true}}}
            });
        };

        makeBar('dailyChart', @json($dailyLabels), @json($dailyValues), 'rgba(79, 159, 108, 0.9)');
        makeBar('monthlyChart', @json($monthlyLabels), @json($monthlyValues), 'rgba(63, 132, 88, 0.9)');
    </script>
</x-app-layout>
