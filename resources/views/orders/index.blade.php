<x-app-layout title="Bronlar" pageTitle="Bronlar">
    <div class="space-y-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Bronlar</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Ixcham va qulay ro'yxat ko'rinishi.</p>
            </div>
            <a href="{{ route('bookings.create') }}" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-primary-700">
                Bron yaratish
            </a>
        </div>

        <div class="hidden overflow-hidden rounded-2xl border border-slate-200/70 bg-white shadow-soft dark:border-slate-800 dark:bg-slate-900 lg:block">
            <table class="w-full table-fixed text-sm">
                <thead class="border-b border-slate-200/70 bg-slate-50 dark:border-slate-800 dark:bg-slate-800/70">
                    <tr class="text-left text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">
                        <th class="w-[25%] px-3 py-2.5">Bron</th>
                        <th class="w-[15%] px-3 py-2.5">Mijoz</th>
                        <th class="w-[11%] px-4 py-2.5">Sana</th>
                        <th class="w-[12%] px-4 py-2.5">Jami</th>
                        <th class="w-[12%] px-4 py-2.5">Qarz</th>
                        <th class="w-[9%] px-3 py-2.5">To'lov turi</th>
                        <th class="w-[8%] px-3 py-2.5">Holat</th>
                        <th class="w-[18%] px-3 py-2.5 text-right">Amallar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/70 dark:divide-slate-800">
                    @forelse($bookings as $booking)
                        <tr>
                            <td class="px-3 py-2.5 align-middle">
                                <div class="flex min-w-0 items-center gap-3">
                                    @if($booking->package_image_path)
                                        <img src="{{ asset('storage/'.$booking->package_image_path) }}" alt="Bron paketi" class="h-12 w-14 shrink-0 rounded-lg object-cover">
                                    @else
                                        <div class="h-12 w-14 shrink-0 rounded-lg bg-slate-100 dark:bg-slate-800"></div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $booking->booking_number }}</p>
                                        <p class="truncate text-xs text-slate-500">{{ $booking->hall?->name ?? '-' }} - {{ $booking->eventType?->name ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-2.5 align-middle">
                                <p class="truncate text-sm text-slate-700 dark:text-slate-200">{{ $booking->client?->full_name ?? 'Mijoz biriktirilmagan' }}</p>
                            </td>
                            <td class="px-4 py-2.5 align-middle whitespace-nowrap text-sm text-slate-700 dark:text-slate-200">
                                {{ optional($booking->event_date)->format('d.m.Y') }}
                            </td>
                            <td class="px-4 py-2.5 align-middle whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">
                                <div class="flex flex-col gap-1 leading-tight">
                                    <span>{{ number_format((float) $booking->total_amount, 2, '.', ' ') }}</span>
                                    <span class="text-[11px] font-semibold text-slate-400">{{ ($booking->currency ?? 'UZS') === 'USD' ? '$' : 'so\'m' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2.5 align-middle whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">
                                <div class="flex flex-col gap-1 leading-tight">
                                    <span>{{ number_format((float) $booking->remaining_amount, 2, '.', ' ') }}</span>
                                    <span class="text-[11px] font-semibold text-slate-400">{{ ($booking->currency ?? 'UZS') === 'USD' ? '$' : 'so\'m' }}</span>
                                </div>
                            </td>
                            <td class="px-3 py-2.5 align-middle whitespace-nowrap">
                                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                    {{ $booking->payment_method ?? 'Naqd' }}
                                </span>
                            </td>
                            <td class="px-3 py-2.5 align-middle whitespace-nowrap">
                                <x-status-badge :status="$booking->status" />
                            </td>
                            <td class="px-3 py-2.5 align-middle">
                                <div class="responsive-actions flex flex-wrap justify-end gap-1.5">
                                    <x-action-link href="{{ route('bookings.show', $booking) }}" icon="eye" variant="view">Ko'rish</x-action-link>
                                    <x-action-link href="{{ route('bookings.edit', $booking) }}" icon="pencil-line" variant="edit">Tahrirlash</x-action-link>
                                    <form action="{{ route('bookings.destroy', $booking) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <x-delete-button />
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-sm text-slate-500">Bronlar topilmadi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="space-y-3 lg:hidden">
            @forelse($bookings as $booking)
                <article class="rounded-2xl border border-slate-200/70 bg-white p-4 shadow-soft dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-start gap-3">
                        @if($booking->package_image_path)
                            <img src="{{ asset('storage/'.$booking->package_image_path) }}" alt="Bron paketi" class="h-16 w-20 shrink-0 rounded-lg object-cover">
                        @else
                            <div class="h-16 w-20 shrink-0 rounded-lg bg-slate-100 dark:bg-slate-800"></div>
                        @endif

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $booking->booking_number }}</p>
                                    <p class="mt-1 truncate text-xs text-slate-500">{{ $booking->hall?->name ?? '-' }} - {{ $booking->eventType?->name ?? '-' }}</p>
                                </div>
                                <x-status-badge :status="$booking->status" />
                            </div>

                            <div class="mt-3 grid grid-cols-2 gap-2 text-xs">
                                <div class="rounded-lg bg-slate-50 px-3 py-2 dark:bg-slate-800/70">
                                    <p class="text-slate-400">Mijoz</p>
                                    <p class="mt-1 truncate text-slate-700 dark:text-slate-200">{{ $booking->client?->full_name ?? 'Biriktirilmagan' }}</p>
                                </div>
                                <div class="rounded-lg bg-slate-50 px-3 py-2 dark:bg-slate-800/70">
                                    <p class="text-slate-400">Sana</p>
                                    <p class="mt-1 text-slate-700 dark:text-slate-200">{{ optional($booking->event_date)->format('d.m.Y') }}</p>
                                </div>
                                <div class="rounded-lg bg-slate-50 px-3 py-2 dark:bg-slate-800/70">
                                    <p class="text-slate-400">Jami</p>
                                    <div class="mt-1 flex flex-col leading-tight text-slate-700 dark:text-slate-200">
                                        <span>{{ number_format((float) $booking->total_amount, 2, '.', ' ') }}</span>
                                        <span class="mt-1 text-[11px] font-semibold text-slate-400">{{ ($booking->currency ?? 'UZS') === 'USD' ? '$' : 'so\'m' }}</span>
                                    </div>
                                </div>
                                <div class="rounded-lg bg-slate-50 px-3 py-2 dark:bg-slate-800/70">
                                    <p class="text-slate-400">Qarz</p>
                                    <div class="mt-1 flex flex-col leading-tight text-slate-700 dark:text-slate-200">
                                        <span>{{ number_format((float) $booking->remaining_amount, 2, '.', ' ') }}</span>
                                        <span class="mt-1 text-[11px] font-semibold text-slate-400">{{ ($booking->currency ?? 'UZS') === 'USD' ? '$' : 'so\'m' }}</span>
                                    </div>
                                </div>
                                <div class="rounded-lg bg-slate-50 px-3 py-2 dark:bg-slate-800/70">
                                    <p class="text-slate-400">To'lov turi</p>
                                    <p class="mt-1 truncate text-slate-700 dark:text-slate-200">{{ $booking->payment_method ?? 'Naqd' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="responsive-actions mt-3 flex gap-2 border-t border-slate-100 pt-3 dark:border-slate-800">
                        <x-action-link href="{{ route('bookings.show', $booking) }}" icon="eye" variant="view" class="flex-1 justify-center py-2">Ko'rish</x-action-link>
                        <x-action-link href="{{ route('bookings.edit', $booking) }}" icon="pencil-line" variant="edit" class="flex-1 justify-center py-2">Tahrirlash</x-action-link>
                        <form action="{{ route('bookings.destroy', $booking) }}" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <x-delete-button class="w-full justify-center py-2" />
                        </form>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-slate-200/70 bg-white px-4 py-10 text-center text-sm text-slate-500 shadow-soft dark:border-slate-800 dark:bg-slate-900">
                    Bronlar topilmadi.
                </div>
            @endforelse
        </div>

        <div>{{ $bookings->links() }}</div>
    </div>
</x-app-layout>
