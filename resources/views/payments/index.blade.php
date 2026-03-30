<x-app-layout title="To'lovlar" pageTitle="Tolovlar">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold">To'lovlar ro'yxati</h2>
        <a href="{{ route('payments.create') }}" class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">To'lov qo'shish</a>
    </div>

    <div class="overflow-x-auto rounded-2xl bg-white shadow-soft dark:bg-slate-900">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left dark:bg-slate-800/70">
            <tr>
                <th class="px-4 py-3">Bron</th>
                <th class="px-4 py-3">Mijoz</th>
                <th class="px-4 py-3">Sana</th>
                <th class="px-4 py-3">Usul</th>
                <th class="px-4 py-3">Miqdor</th>
                <th class="px-4 py-3 text-right">Amallar</th>
            </tr>
            </thead>
            <tbody>
            @forelse($payments as $payment)
                <tr class="border-t border-slate-100 dark:border-slate-800">
                    <td class="px-4 py-3 font-medium">{{ $payment->booking?->booking_number }}</td>
                    <td class="px-4 py-3">{{ $payment->booking?->client?->full_name ?: '—' }}</td>
                    <td class="px-4 py-3">{{ $payment->payment_date?->format('d.m.Y') }}</td>
                    <td class="px-4 py-3">{{ $payment->payment_method }}</td>
                    <td class="px-4 py-3">{{ number_format($payment->amount, 0, '.', ' ') }} so'm</td>
                    <td class="px-4 py-3"><div class="flex justify-end gap-2"><a href="{{ route('payments.edit', $payment) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800">Tahrirlash</a><form action="{{ route('payments.destroy', $payment) }}" method="POST">@csrf @method('DELETE')<x-delete-button /></form></div></td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-6 text-center text-slate-500">To'lovlar topilmadi.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $payments->links() }}</div>
</x-app-layout>

