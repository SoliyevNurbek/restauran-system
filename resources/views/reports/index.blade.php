<x-app-layout title="Hisobotlar" pageTitle="Hisobotlar">
    <div x-data="reportDashboard(@js($reportPeriods))" class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-stat-card title="Jami kirim" :value="number_format($totalPurchases, 2, '.', ' ')" suffix="so'm" icon="package-plus" />
            <x-stat-card title="Jami xarajat" :value="number_format($totalExpenses, 2, '.', ' ')" suffix="so'm" icon="wallet-cards" />
            <x-stat-card title="Ta'minotchilar" :value="$totalSuppliers" icon="truck" />
            <x-stat-card title="Kam qolgan mahsulot" :value="$lowStockCount" icon="triangle-alert" />
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-[2rem] bg-gradient-to-br from-emerald-500 to-emerald-600 p-5 text-white shadow-soft">
                <p class="text-xs uppercase tracking-[0.24em] text-emerald-100">Foyda</p>
                <p class="mt-3 text-3xl font-semibold" x-text="formatMoney(current.profit)"></p>
            </div>
            <div class="rounded-[2rem] bg-gradient-to-br from-rose-500 to-rose-600 p-5 text-white shadow-soft">
                <p class="text-xs uppercase tracking-[0.24em] text-rose-100">Ziyon</p>
                <p class="mt-3 text-3xl font-semibold" x-text="formatMoney(current.loss)"></p>
            </div>
            <div class="rounded-[2rem] border border-slate-200/70 bg-white p-5 shadow-soft dark:border-slate-800 dark:bg-slate-900">
                <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Umumiy summa</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900 dark:text-white" x-text="formatMoney(current.total)"></p>
            </div>
        </div>

        <div class="grid gap-6 2xl:grid-cols-3">
            <div class="rounded-[2rem] border border-slate-200/70 bg-white p-5 shadow-soft dark:border-slate-800 dark:bg-slate-900 xl:col-span-2">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Dinamik hisobot</p>
                            <h3 class="mt-2 break-words text-xl font-semibold text-slate-900 dark:text-white" x-text="current.title"></h3>
                        </div>
                        <div class="grid w-full grid-cols-2 gap-2 sm:grid-cols-3 lg:w-auto lg:grid-cols-5">
                            <template x-for="period in periodOrder" :key="period.key">
                                <button
                                    type="button"
                                    @click="changePeriod(period.key)"
                                    class="min-w-0 rounded-2xl px-3 py-2 text-center text-xs font-semibold transition"
                                    :class="active === period.key
                                        ? 'bg-slate-950 text-white dark:bg-white dark:text-slate-950'
                                        : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800'"
                                    x-text="period.label">
                                </button>
                            </template>
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-2 2xl:grid-cols-4">
                        <div class="rounded-2xl bg-emerald-50 px-4 py-3 dark:bg-emerald-950/20">
                            <p class="text-xs text-emerald-600 dark:text-emerald-300">Kirim</p>
                            <p class="mt-1 text-lg font-semibold text-emerald-700 dark:text-emerald-300" x-text="formatMoney(current.purchaseTotal)"></p>
                        </div>
                        <div class="rounded-2xl bg-rose-50 px-4 py-3 dark:bg-rose-950/20">
                            <p class="text-xs text-rose-600 dark:text-rose-300">Xarajat</p>
                            <p class="mt-1 text-lg font-semibold text-rose-700 dark:text-rose-300" x-text="formatMoney(current.expenseTotal)"></p>
                        </div>
                    </div>
                </div>
                <div class="mt-6 h-[260px] sm:h-[300px] lg:h-[340px] 2xl:h-[360px]">
                    <canvas id="reportChart" class="h-full w-full"></canvas>
                </div>
            </div>

            <div class="rounded-[2rem] border border-slate-200/70 bg-white p-5 shadow-soft dark:border-slate-800 dark:bg-slate-900">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Kategoriya bo'yicha xarajat</h3>
                <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-1 2xl:grid-cols-3">
                    @forelse($expenseByCategory as $category)
                        <div class="flex flex-col gap-2 rounded-2xl border border-slate-100 px-4 py-3 text-sm dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
                            <span class="min-w-0 break-words">{{ $category->name }}</span>
                            <x-money :value="$category->total" class="font-semibold" />
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Kategoriyalar bo'yicha ma'lumot yo'q.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="rounded-3xl bg-white p-5 shadow-soft dark:bg-slate-900">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Ko'p olinadigan mahsulotlar</h3>
                <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-1 2xl:grid-cols-2">
                    @foreach($topProducts as $product)
                        <div class="flex flex-col gap-2 rounded-2xl border border-slate-100 px-4 py-3 text-sm dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
                            <span class="min-w-0 break-words">{{ $product->name }}</span>
                            <span class="inline-flex items-center gap-2 font-semibold">
                                <span>{{ number_format($product->quantity, 3) }}</span>
                                <x-unit-badge :value="$product->unit" />
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-3xl bg-white p-5 shadow-soft dark:bg-slate-900 xl:col-span-2">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Ta'minotchi kesimidagi qarzdorlik</h3>
                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    @foreach($topSuppliers as $supplier)
                        <div class="rounded-2xl border border-slate-100 px-4 py-3 dark:border-slate-800">
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                <span class="min-w-0 break-words text-sm font-medium">{{ $supplier->full_name }}</span>
                                <x-money :value="$supplier->balance" class="text-sm font-semibold" />
                            </div>
                            <div class="mt-2 grid gap-1 text-xs text-slate-500 sm:grid-cols-2">
                                <p class="break-words">Kirim: {{ number_format($supplier->purchases_sum_total_amount ?? 0, 2, '.', ' ') }} so'm</p>
                                <p class="break-words">To'lov: {{ number_format($supplier->payments_sum_amount ?? 0, 2, '.', ' ') }} so'm</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        function reportDashboard(periods) {
            return {
                periods,
                active: 'daily',
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
                    const chartElement = document.getElementById('reportChart');
                    if (!chartElement) return;

                    const dataset = this.current;
                    if (this.chart) {
                        this.chart.destroy();
                    }

                    this.chart = new Chart(chartElement, {
                        type: 'bar',
                        data: {
                            labels: dataset.labels,
                            datasets: [
                                {label: 'Kirim', data: dataset.purchaseValues, backgroundColor: 'rgba(16, 185, 129, 0.88)', borderRadius: 12, borderSkipped: false},
                                {label: 'Xarajat', data: dataset.expenseValues, backgroundColor: 'rgba(244, 63, 94, 0.82)', borderRadius: 12, borderSkipped: false}
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        boxWidth: 8,
                                    }
                                },
                                tooltip: {
                                    backgroundColor: '#0f172a',
                                    padding: 12,
                                    cornerRadius: 14,
                                    titleColor: '#f8fafc',
                                    bodyColor: '#e2e8f0',
                                }
                            },
                            scales: {
                                x: {
                                    grid: {display: false},
                                    ticks: {
                                        maxRotation: 0,
                                        color: '#64748b',
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(148, 163, 184, 0.12)',
                                    },
                                    ticks: {
                                        color: '#64748b',
                                    },
                                    border: {
                                        display: false,
                                    }
                                }
                            }
                        }
                    });
                },
                formatMoney(value) {
                    return `${new Intl.NumberFormat('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}).format(value || 0)} so'm`;
                }
            }
        }
    </script>
</x-app-layout>
