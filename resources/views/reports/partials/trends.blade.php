@php
    $defaultPeriod = 'weekly';
@endphp

<div x-data="reportDashboard(@js($reportPeriods), '{{ $defaultPeriod }}')" class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card title="Kunlik oqim" :value="number_format($reportPeriods['daily']['purchaseTotal'], 0, '.', ' ')" suffix="so'm" icon="calendar-range" />
        <x-stat-card title="Haftalik oqim" :value="number_format($reportPeriods['weekly']['purchaseTotal'], 0, '.', ' ')" suffix="so'm" icon="bar-chart-3" />
        <x-stat-card title="30 kun xarajat" :value="number_format($reportPeriods['monthly']['expenseTotal'], 0, '.', ' ')" suffix="so'm" icon="receipt-text" />
        <x-stat-card title="12 oy aylanma" :value="number_format($reportPeriods['yearly']['total'], 0, '.', ' ')" suffix="so'm" icon="line-chart" />
    </div>

    <div class="grid gap-6 2xl:grid-cols-[1.45fr_0.55fr]">
        <x-admin.section-card icon="chart-line" title="Davriy trendlar" subtitle="Kirim va xarajatlar tanlangan davr kesimida jonli taqqoslanadi.">
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Davr tanlovi</p>
                        <h3 class="mt-2 break-words text-xl font-semibold text-slate-900 dark:text-white" x-text="current.title"></h3>
                    </div>
                    <div class="grid w-full grid-cols-2 gap-2 sm:grid-cols-3 lg:w-auto lg:grid-cols-5">
                        <template x-for="period in periodOrder" :key="period.key">
                            <button type="button"
                                    @click="changePeriod(period.key)"
                                    class="min-w-0 rounded-2xl px-3 py-2 text-center text-xs font-semibold transition"
                                    :class="active === period.key ? 'bg-slate-950 text-white dark:bg-white dark:text-slate-950' : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800'">
                                <span x-text="period.label"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 2xl:grid-cols-4">
                    <div class="rounded-2xl bg-emerald-50 px-4 py-3 dark:bg-emerald-950/20">
                        <p class="text-xs text-emerald-600 dark:text-emerald-300">Kirim</p>
                        <p class="mt-1 text-lg font-semibold text-emerald-700 dark:text-emerald-300" x-text="formatMoney(current.purchaseTotal)"></p>
                    </div>
                    <div class="rounded-2xl bg-rose-50 px-4 py-3 dark:bg-rose-950/20">
                        <p class="text-xs text-rose-600 dark:text-rose-300">Xarajat</p>
                        <p class="mt-1 text-lg font-semibold text-rose-700 dark:text-rose-300" x-text="formatMoney(current.expenseTotal)"></p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-800/60">
                        <p class="text-xs text-slate-500">Foyda</p>
                        <p class="mt-1 text-lg font-semibold text-slate-900 dark:text-white" x-text="formatMoney(current.profit)"></p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-800/60">
                        <p class="text-xs text-slate-500">Aylanma</p>
                        <p class="mt-1 text-lg font-semibold text-slate-900 dark:text-white" x-text="formatMoney(current.total)"></p>
                    </div>
                </div>
            </div>

            <div class="mt-6 h-[280px] sm:h-[340px]">
                <canvas id="reportTrendChart" class="h-full w-full"></canvas>
            </div>
        </x-admin.section-card>

        <x-admin.section-card icon="layers-3" title="Davrlar solishtiruv" subtitle="Turli interval bo'yicha qisqa summary.">
            <div class="space-y-3">
                @foreach([
                    ['label' => '7 kun', 'data' => $reportPeriods['daily']],
                    ['label' => '8 hafta', 'data' => $reportPeriods['weekly']],
                    ['label' => '30 kun', 'data' => $reportPeriods['monthly']],
                    ['label' => '6 oy', 'data' => $reportPeriods['half_year']],
                ] as $period)
                    <div class="rounded-2xl border border-slate-100 px-4 py-4 dark:border-slate-800">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $period['label'] }}</p>
                            <span class="text-xs text-slate-400">{{ number_format($period['data']['purchaseTotal'] - $period['data']['expenseTotal'], 0, '.', ' ') }} so'm</span>
                        </div>
                        <div class="mt-2 space-y-1 text-xs text-slate-500">
                            <p>Kirim: {{ number_format($period['data']['purchaseTotal'], 0, '.', ' ') }} so'm</p>
                            <p>Xarajat: {{ number_format($period['data']['expenseTotal'], 0, '.', ' ') }} so'm</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-admin.section-card>
    </div>

    <script>
        function reportDashboard(periods, initialPeriod) {
            return {
                periods,
                active: initialPeriod,
                chart: null,
                periodOrder: [
                    {key: 'daily', label: 'Kunlik'},
                    {key: 'weekly', label: 'Haftalik'},
                    {key: 'monthly', label: 'Oylik'},
                    {key: 'half_year', label: '6 oy'},
                    {key: 'yearly', label: 'Yillik'},
                ],
                get current() {
                    return this.periods[this.active];
                },
                init() {
                    this.$nextTick(() => this.renderChart());
                },
                changePeriod(period) {
                    this.active = period;
                    this.renderChart();
                },
                renderChart() {
                    const chartElement = document.getElementById('reportTrendChart');
                    if (!chartElement) return;

                    if (this.chart) {
                        this.chart.destroy();
                    }

                    this.chart = new Chart(chartElement, {
                        type: 'bar',
                        data: {
                            labels: this.current.labels,
                            datasets: [
                                {
                                    label: 'Kirim',
                                    data: this.current.purchaseValues,
                                    backgroundColor: 'rgba(16, 185, 129, 0.88)',
                                    borderRadius: 12,
                                    borderSkipped: false
                                },
                                {
                                    label: 'Xarajat',
                                    data: this.current.expenseValues,
                                    backgroundColor: 'rgba(244, 63, 94, 0.82)',
                                    borderRadius: 12,
                                    borderSkipped: false
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {mode: 'index', intersect: false},
                            plugins: {
                                legend: {position: 'bottom', labels: {usePointStyle: true, boxWidth: 8}},
                                tooltip: {
                                    backgroundColor: '#0f172a',
                                    padding: 12,
                                    cornerRadius: 14,
                                    titleColor: '#f8fafc',
                                    bodyColor: '#e2e8f0',
                                }
                            },
                            scales: {
                                x: {grid: {display: false}, ticks: {maxRotation: 0, color: '#64748b'}},
                                y: {
                                    beginAtZero: true,
                                    grid: {color: 'rgba(148, 163, 184, 0.12)'},
                                    ticks: {color: '#64748b'},
                                    border: {display: false}
                                }
                            }
                        }
                    });
                },
                formatMoney(value) {
                    return `${new Intl.NumberFormat('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 0}).format(value || 0)} so'm`;
                }
            }
        }
    </script>
</div>
