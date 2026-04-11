<x-app-layout title="To'lovlar" pageTitle="To'lovlar" pageSubtitle="Tushumlar, qisman to'langan bronlar va moliyaviy intizomni premium jadval orqali kuzating.">
    <div class="space-y-5">
        <x-admin.page-intro eyebrow="Moliya" title="To'lovlar" subtitle="Bron raqami, mijoz, usul va to'lov sanasi bo'yicha tushumlarni tez toping.">
            <x-slot:actions>
                <a href="{{ route('payments.create') }}" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    To'lov qo'shish
                </a>
            </x-slot:actions>
        </x-admin.page-intro>

        <x-admin.filter-shell>
            <form method="GET" class="grid gap-3 md:grid-cols-[1.4fr_0.9fr_auto]">
                <input type="text" name="q" value="{{ $filters['search'] }}" placeholder="Bron yoki mijoz bo'yicha qidiring" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slate-400 dark:border-slate-700 dark:bg-slate-950">
                <select name="method" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slate-400 dark:border-slate-700 dark:bg-slate-950">
                    <option value="">Barcha usullar</option>
                    @foreach($methods as $method)
                        <option value="{{ $method }}" @selected($filters['method'] === $method)>{{ $method }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <button class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white dark:bg-white dark:text-slate-950">Filtrlash</button>
                    <a href="{{ route('payments.index') }}" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 dark:border-slate-700 dark:text-slate-300">Tozalash</a>
                </div>
            </form>
        </x-admin.filter-shell>

        @if($payments->count())
            <div class="overflow-hidden rounded-[30px] border border-slate-200/80 bg-white shadow-soft dark:border-slate-800 dark:bg-slate-900">
                <div class="mobile-fit-table overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50/90 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400 dark:bg-slate-950/70">
                            <tr>
                                <th class="px-5 py-4">Bron</th>
                                <th class="px-5 py-4">Mijoz</th>
                                <th class="px-5 py-4">Moliyaviy holat</th>
                                <th class="px-5 py-4">Usul</th>
                                <th class="px-5 py-4">Sana</th>
                                <th class="px-5 py-4 text-right">Amallar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($payments as $payment)
                                @php
                                    $booking = $payment->booking;
                                    $paymentStatus = ! $booking ? "Kutilmoqda" : ((float) $booking->remaining_amount <= 0 ? "To'langan" : ((float) $booking->paid_amount > 0 ? "Qisman to'langan" : "Kutilmoqda"));
                                @endphp
                                <tr class="transition hover:bg-slate-50/70 dark:hover:bg-slate-950/40">
                                    <td class="px-5 py-4 font-semibold text-slate-900 dark:text-white">{{ $booking?->booking_number ?? 'Bron yo‘q' }}</td>
                                    <td class="px-5 py-4">
                                        <p class="font-medium text-slate-900 dark:text-white">{{ $booking?->client?->full_name ?? 'Mijoz yo‘q' }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $booking?->hall?->name ?? 'Zal yo‘q' }}</p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <p class="font-semibold text-slate-900 dark:text-white">{{ number_format($payment->amount, 0, '.', ' ') }} UZS</p>
                                        <p class="mt-1 text-xs text-slate-500">Qoldiq: {{ number_format((float) ($booking?->remaining_amount ?? 0), 0, '.', ' ') }} UZS</p>
                                        <div class="mt-2 inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300">{{ $paymentStatus }}</div>
                                    </td>
                                    <td class="px-5 py-4">{{ $payment->payment_method }}</td>
                                    <td class="px-5 py-4">{{ $payment->payment_date?->format('d.m.Y') }}</td>
                                    <td class="px-5 py-4">
                                        <div class="responsive-actions flex justify-end gap-2">
                                            <x-action-link href="{{ route('payments.edit', $payment) }}" icon="pencil-line" variant="edit">Tahrirlash</x-action-link>
                                            <form action="{{ route('payments.destroy', $payment) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <x-delete-button />
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <x-admin.empty-state icon="wallet" title="To'lovlar topilmadi" text="Tushum yozuvlari hali kiritilmagan. Yangi to'lov qo'shib moliyaviy oqimni boshlang." action-href="{{ route('payments.create') }}" action-label="To'lov qo'shish" />
        @endif

        <div>{{ $payments->links() }}</div>
    </div>
</x-app-layout>
