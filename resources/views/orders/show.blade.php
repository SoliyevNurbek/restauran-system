<x-app-layout title="Bron tafsilotlari" pageTitle="Bron tafsilotlari">
    <div class="space-y-6">
        <div class="grid gap-6 xl:grid-cols-[minmax(320px,0.9fr)_minmax(0,1.1fr)]">
            <section class="rounded-2xl bg-white p-4 shadow-soft dark:bg-slate-900 sm:p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0">
                        <h3 class="truncate text-lg font-semibold text-slate-900 dark:text-white">{{ $booking->booking_number }}</h3>
                        <p class="mt-1 text-sm text-slate-500">{{ $booking->hall?->name ?? '-' }} - {{ $booking->eventType?->name ?? '-' }}</p>
                    </div>
                    <x-status-badge :status="$booking->status" />
                </div>

                @if($booking->package_image_path)
                    <img src="{{ asset('storage/'.$booking->package_image_path) }}" alt="Tanlangan paket rasmi" class="mt-4 h-48 w-full rounded-2xl object-cover sm:h-56">
                @endif

                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-xl bg-slate-50 px-4 py-3 dark:bg-slate-800/70">
                        <p class="text-xs uppercase tracking-[0.14em] text-slate-400">Mijoz</p>
                        <p class="mt-1 text-sm font-medium text-slate-800 dark:text-slate-100">{{ $booking->client?->full_name ?? 'Mijoz biriktirilmagan' }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 px-4 py-3 dark:bg-slate-800/70">
                        <p class="text-xs uppercase tracking-[0.14em] text-slate-400">Sana</p>
                        <p class="mt-1 text-sm font-medium text-slate-800 dark:text-slate-100">{{ optional($booking->event_date)->format('d.m.Y') }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 px-4 py-3 dark:bg-slate-800/70">
                        <p class="text-xs uppercase tracking-[0.14em] text-slate-400">Paket</p>
                        <p class="mt-1 text-sm font-medium text-slate-800 dark:text-slate-100">{{ $booking->package?->name ?? 'Tanlanmagan' }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 px-4 py-3 dark:bg-slate-800/70">
                        <p class="text-xs uppercase tracking-[0.14em] text-slate-400">To'lov turi</p>
                        <p class="mt-1 text-sm font-medium text-slate-800 dark:text-slate-100">{{ $booking->payment_method ?? 'Naqd' }}</p>
                    </div>
                </div>

                <div class="mt-5 grid gap-3">
                    <div class="flex items-center justify-between rounded-xl border border-slate-200/70 px-4 py-3 dark:border-slate-800">
                        <span class="text-sm text-slate-500">Tushum</span>
                        <x-money :value="$booking->total_amount" class="font-semibold" />
                    </div>
                    <div class="flex items-center justify-between rounded-xl border border-slate-200/70 px-4 py-3 dark:border-slate-800">
                        <span class="text-sm text-slate-500">Oshxona xarajatlari</span>
                        <x-money :value="$booking->kitchen_costs_total" class="font-semibold" />
                    </div>
                    <div class="flex items-center justify-between rounded-xl border border-slate-200/70 px-4 py-3 dark:border-slate-800">
                        <span class="text-sm text-slate-500">Tadbir xarajatlari</span>
                        <x-money :value="$booking->event_costs_total" class="font-semibold" />
                    </div>
                    <div class="flex items-center justify-between rounded-xl border border-slate-200/70 px-4 py-3 dark:border-slate-800">
                        <span class="text-sm text-slate-500">Doimiy xarajatlar</span>
                        <x-money :value="$booking->fixed_costs_total" class="font-semibold" />
                    </div>
                    <div class="flex items-center justify-between rounded-xl border border-slate-200/70 px-4 py-3 dark:border-slate-800">
                        <span class="text-sm text-slate-500">Jami xarajat</span>
                        <x-money :value="$booking->total_costs" class="font-semibold" />
                    </div>
                    <div class="flex items-center justify-between rounded-xl border border-emerald-200/70 bg-emerald-50 px-4 py-3 dark:border-emerald-900/40 dark:bg-emerald-950/20">
                        <span class="text-sm font-medium text-emerald-700 dark:text-emerald-300">Foyda</span>
                        <x-money :value="$booking->profit" class="font-semibold text-emerald-700 dark:text-emerald-300" />
                    </div>
                </div>
            </section>

            <section class="rounded-2xl bg-white p-4 shadow-soft dark:bg-slate-900 sm:p-6">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Qo'shimcha xizmatlar</h3>
                    <span class="text-xs text-slate-400">{{ $booking->services->count() }} ta</span>
                </div>

                <div class="hidden md:block">
                    <table class="w-full table-fixed text-sm">
                        <thead class="border-b border-slate-200/70 text-left text-slate-500 dark:border-slate-800">
                            <tr>
                                <th class="w-[40%] pb-3">Xizmat</th>
                                <th class="w-[15%] pb-3">Miqdor</th>
                                <th class="w-[22%] pb-3">Narx</th>
                                <th class="w-[23%] pb-3">Jami</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/70 dark:divide-slate-800">
                            @forelse($booking->services as $service)
                                <tr>
                                    <td class="py-3 pr-3">{{ $service->service?->name }}</td>
                                    <td class="py-3 pr-3">{{ $service->quantity }}</td>
                                    <td class="py-3 pr-3"><x-money :value="$service->price" /></td>
                                    <td class="py-3"><x-money :value="$service->total" /></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-slate-500">Qo'shimcha xizmatlar yo'q.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="space-y-3 md:hidden">
                    @forelse($booking->services as $service)
                        <div class="rounded-xl border border-slate-200/70 px-4 py-3 dark:border-slate-800">
                            <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $service->service?->name }}</p>
                            <div class="mt-3 grid grid-cols-3 gap-2 text-xs">
                                <div>
                                    <p class="text-slate-400">Miqdor</p>
                                    <p class="mt-1 text-slate-700 dark:text-slate-200">{{ $service->quantity }}</p>
                                </div>
                                <div>
                                    <p class="text-slate-400">Narx</p>
                                    <div class="mt-1 text-slate-700 dark:text-slate-200"><x-money :value="$service->price" /></div>
                                </div>
                                <div>
                                    <p class="text-slate-400">Jami</p>
                                    <div class="mt-1 text-slate-700 dark:text-slate-200"><x-money :value="$service->total" /></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-slate-200/70 px-4 py-6 text-center text-sm text-slate-500 dark:border-slate-800">
                            Qo'shimcha xizmatlar yo'q.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
