<x-app-layout title="Kirimlar" pageTitle="Kirimlar">
    <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Kirimlar ro'yxati</h2>
            <p class="text-sm text-slate-500">Ta'minotchi, sana, mahsulotlar va umumiy summa bilan kirim nazorati</p>
        </div>
        <a href="{{ route('purchases.create') }}" class="rounded-2xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Kirim qo'shish</a>
    </div>

    <div class="space-y-4">
        @forelse($purchases as $purchase)
            <div class="rounded-3xl bg-white p-5 shadow-soft dark:bg-slate-900">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ $purchase->supplier?->full_name }}</h3>
                        <p class="mt-1 text-sm text-slate-500">{{ optional($purchase->purchase_date)->format('d.m.Y') }} | {{ $purchase->supplier?->company_name ?: 'Kompaniya ko\'rsatilmagan' }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="rounded-2xl bg-primary-50 px-4 py-2 text-sm font-semibold text-primary-700 dark:bg-primary-950/40 dark:text-primary-300">{{ number_format($purchase->total_amount, 2) }}</span>
                        <a href="{{ route('purchases.edit', $purchase) }}" class="rounded-2xl bg-slate-900 px-4 py-2 text-sm font-medium text-white dark:bg-slate-100 dark:text-slate-900">Tahrirlash</a>
                        <form method="POST" action="{{ route('purchases.destroy', $purchase) }}">
                            @csrf
                            @method('DELETE')
                            <x-delete-button />
                        </form>
                    </div>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="text-slate-500 dark:text-slate-300">
                        <tr>
                            <th class="pb-2 pr-4">Mahsulot</th>
                            <th class="pb-2 pr-4">Miqdor</th>
                            <th class="pb-2 pr-4">Narx</th>
                            <th class="pb-2">Jami</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($purchase->items as $item)
                            <tr class="border-t border-slate-100 dark:border-slate-800">
                                <td class="py-3 pr-4">{{ $item->product?->name }}</td>
                                <td class="py-3 pr-4">{{ number_format($item->quantity, 3) }} {{ $item->product?->unit }}</td>
                                <td class="py-3 pr-4">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="py-3 font-semibold">{{ number_format($item->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                @if($purchase->notes)
                    <p class="mt-4 text-sm text-slate-500">{{ $purchase->notes }}</p>
                @endif
            </div>
        @empty
            <div class="rounded-3xl bg-white p-8 text-center text-sm text-slate-500 shadow-soft dark:bg-slate-900">Kirimlar hali mavjud emas.</div>
        @endforelse
    </div>

    <div class="mt-5">{{ $purchases->links() }}</div>
</x-app-layout>
