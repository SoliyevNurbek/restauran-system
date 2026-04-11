@php
    $workflow = [
        'Yangi so\'rov' => ['label' => "Yangi so'rov", 'icon' => 'sparkles'],
        'Yangi' => ['label' => 'Yangi', 'icon' => 'clipboard-plus'],
        'Tasdiqlangan' => ['label' => 'Tasdiqlangan', 'icon' => 'badge-check'],
        'Avans olingan' => ['label' => 'Avans olingan', 'icon' => 'wallet'],
        'Tayyorlanmoqda' => ['label' => 'Tayyorlanmoqda', 'icon' => 'chef-hat'],
        'Tadbir bo\'lib o\'tdi' => ['label' => "Tadbir bo'lib o'tdi", 'icon' => 'party-popper'],
        'Otkazildi' => ['label' => "O'tkazildi", 'icon' => 'party-popper'],
        'Yakunlandi' => ['label' => 'Yakunlandi', 'icon' => 'check-check'],
        'Bekor qilindi' => ['label' => 'Bekor qilindi', 'icon' => 'x-circle'],
    ];
    $currentStatus = $booking->status;
@endphp

<x-app-layout title="Bron tafsilotlari" pageTitle="Bron tafsilotlari" pageSubtitle="Bron bo'yicha mijoz, zal, xizmatlar, to'lov va status jarayonini bitta sahifada ko'ring.">
    <div class="space-y-6">
        <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
            <x-admin.section-card :title="$booking->booking_number" :subtitle="($booking->hall?->name ?? 'Zal yo‘q').' · '.($booking->eventType?->name ?? 'Tadbir turi yo‘q')" icon="calendar-days">
                @if($booking->package_image_url)
                    <img src="{{ $booking->package_image_url }}" alt="Tanlangan paket rasmi" class="mb-5 h-56 w-full rounded-[26px] object-cover">
                @endif

                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[22px] bg-slate-50 px-4 py-4 dark:bg-slate-800/70">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Mijoz</p>
                        <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">{{ $booking->client?->full_name ?? 'Mijoz biriktirilmagan' }}</p>
                    </div>
                    <div class="rounded-[22px] bg-slate-50 px-4 py-4 dark:bg-slate-800/70">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Sana va vaqt</p>
                        <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">{{ optional($booking->event_date)->format('d.m.Y') }} · {{ $booking->start_time }} - {{ $booking->end_time }}</p>
                    </div>
                    <div class="rounded-[22px] bg-slate-50 px-4 py-4 dark:bg-slate-800/70">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Paket / xizmat</p>
                        <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">{{ $booking->package?->name ?? 'Paket tanlanmagan' }}</p>
                    </div>
                    <div class="rounded-[22px] bg-slate-50 px-4 py-4 dark:bg-slate-800/70">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-400">To'lov turi</p>
                        <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">{{ $booking->payment_method ?? 'Naqd' }}</p>
                    </div>
                    <div class="rounded-[22px] bg-slate-50 px-4 py-4 dark:bg-slate-800/70">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Mehmon soni</p>
                        <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">{{ $booking->guest_count }} ta</p>
                    </div>
                    <div class="rounded-[22px] bg-slate-50 px-4 py-4 dark:bg-slate-800/70">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Joriy holat</p>
                        <div class="mt-2"><x-status-badge :status="$booking->status" /></div>
                    </div>
                </div>

                <div class="mt-5 grid gap-3">
                    <div class="flex items-center justify-between rounded-[22px] border border-slate-200/80 px-4 py-3 dark:border-slate-800">
                        <span class="text-sm text-slate-500">Umumiy summa</span>
                        <x-money :value="$booking->total_amount" class="font-semibold" />
                    </div>
                    <div class="flex items-center justify-between rounded-[22px] border border-slate-200/80 px-4 py-3 dark:border-slate-800">
                        <span class="text-sm text-slate-500">Avans</span>
                        <x-money :value="$booking->advance_amount" class="font-semibold" />
                    </div>
                    <div class="flex items-center justify-between rounded-[22px] border border-slate-200/80 px-4 py-3 dark:border-slate-800">
                        <span class="text-sm text-slate-500">To'langan</span>
                        <x-money :value="$booking->paid_amount" class="font-semibold" />
                    </div>
                    <div class="flex items-center justify-between rounded-[22px] border border-amber-200/80 bg-amber-50 px-4 py-3 dark:border-amber-900/40 dark:bg-amber-950/20">
                        <span class="text-sm font-medium text-amber-700 dark:text-amber-300">Qolgan to'lov</span>
                        <x-money :value="$booking->remaining_amount" class="font-semibold text-amber-700 dark:text-amber-300" />
                    </div>
                </div>
            </x-admin.section-card>

            <div class="space-y-6">
                <x-admin.section-card title="Bron workflow" subtitle="Toyxona operatsion jarayonining bosqichma-bosqich ko'rinishi." icon="git-branch">
                    <div class="grid gap-3 md:grid-cols-2">
                        @foreach($workflow as $status => $meta)
                            @php
                                $isCurrent = $currentStatus === $status || ($status === 'Tadbir bo\'lib o\'tdi' && $currentStatus === 'Otkazildi');
                                $isReached = in_array($status, array_slice(array_keys($workflow), 0, array_search($currentStatus === 'Otkazildi' ? 'Tadbir bo\'lib o\'tdi' : $currentStatus, array_keys($workflow), true) + 1), true);
                            @endphp
                            <div class="rounded-[22px] border px-4 py-4 {{ $isCurrent ? 'border-slate-900 bg-slate-900 text-white dark:border-white dark:bg-white dark:text-slate-950' : ($isReached ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-900/40 dark:bg-emerald-950/20' : 'border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-950/50') }}">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl {{ $isCurrent ? 'bg-white/15 dark:bg-slate-200' : 'bg-white dark:bg-slate-900' }}">
                                        <i data-lucide="{{ $meta['icon'] }}" class="h-5 w-5"></i>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold">{{ $meta['label'] }}</p>
                                        <p class="mt-1 text-xs {{ $isCurrent ? 'text-white/70 dark:text-slate-500' : 'text-slate-500' }}">{{ $isCurrent ? 'Joriy bosqich' : ($isReached ? 'Bosib o‘tilgan' : 'Kutilyapti') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-admin.section-card>

                <x-admin.section-card title="Qo'shimcha xizmatlar" subtitle="Bron tarkibiga kirgan xizmatlar va summalar." icon="sparkles">
                    <div class="space-y-3">
                        @forelse($booking->services as $service)
                            <div class="rounded-[22px] border border-slate-200/80 px-4 py-4 dark:border-slate-800">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $service->service?->name }}</p>
                                    <x-money :value="$service->total" class="font-semibold" />
                                </div>
                                <div class="mt-2 grid grid-cols-2 gap-2 text-xs text-slate-500">
                                    <p>Miqdor: {{ $service->quantity }}</p>
                                    <p>Narx: {{ number_format((float) $service->price, 0, '.', ' ') }} UZS</p>
                                </div>
                            </div>
                        @empty
                            <x-admin.empty-state icon="sparkles" title="Qo'shimcha xizmatlar yo'q" text="Bu bron uchun qo'shimcha xizmatlar biriktirilmagan." />
                        @endforelse
                    </div>
                </x-admin.section-card>

                <x-admin.section-card title="Xarajat va foyda" subtitle="Bron rentabelligini tez ko'rish uchun moliyaviy kesim." icon="wallet-cards">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rounded-[22px] bg-slate-50 px-4 py-4 dark:bg-slate-800/70">
                            <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Oshxona xarajati</p>
                            <x-money :value="$booking->kitchen_costs_total" class="mt-2 text-lg font-semibold" />
                        </div>
                        <div class="rounded-[22px] bg-slate-50 px-4 py-4 dark:bg-slate-800/70">
                            <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Tadbir xarajati</p>
                            <x-money :value="$booking->event_costs_total" class="mt-2 text-lg font-semibold" />
                        </div>
                        <div class="rounded-[22px] bg-slate-50 px-4 py-4 dark:bg-slate-800/70">
                            <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Doimiy xarajat</p>
                            <x-money :value="$booking->fixed_costs_total" class="mt-2 text-lg font-semibold" />
                        </div>
                        <div class="rounded-[22px] bg-emerald-50 px-4 py-4 dark:bg-emerald-950/20">
                            <p class="text-xs uppercase tracking-[0.18em] text-emerald-600 dark:text-emerald-300">Foyda</p>
                            <x-money :value="$booking->profit" class="mt-2 text-lg font-semibold text-emerald-700 dark:text-emerald-300" />
                        </div>
                    </div>
                </x-admin.section-card>
            </div>
        </div>
    </div>
</x-app-layout>
