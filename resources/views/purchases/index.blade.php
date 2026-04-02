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
                <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                    <div class="min-w-0">
                        <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ $purchase->supplier?->full_name }}</h3>
                        <p class="mt-1 text-sm text-slate-500">{{ optional($purchase->purchase_date)->format('d.m.Y') }} | {{ $purchase->supplier?->company_name ?: 'Kompaniya ko\'rsatilmagan' }}</p>
                    </div>
                    <div class="responsive-actions flex flex-nowrap items-center gap-2 overflow-x-auto pb-1">
                        <x-money :value="$purchase->total_amount" class="rounded-2xl bg-gradient-to-r from-emerald-500 to-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm dark:from-emerald-600 dark:to-emerald-500" suffixClass="text-xs font-medium text-emerald-100" />
                        <x-action-link href="{{ route('purchases.edit', $purchase) }}" icon="pencil-line" variant="edit">Tahrirlash</x-action-link>
                        <form method="POST" action="{{ route('purchases.destroy', $purchase) }}" class="shrink-0">
                            @csrf
                            @method('DELETE')
                            <x-delete-button />
                        </form>
                    </div>
                </div>

                <div class="mt-4 space-y-3 lg:hidden">
                    @foreach($purchase->items as $item)
                        <div class="rounded-2xl border border-slate-200/70 p-4 dark:border-slate-800">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $item->product?->name }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ number_format($item->quantity, 3) }} {{ $item->product?->unit ?? '-' }}</p>
                                </div>
                                <x-money :value="$item->line_total" class="text-sm font-semibold text-slate-900 dark:text-white" suffixClass="text-[11px] font-medium text-slate-400 dark:text-slate-500" />
                            </div>

                            <div class="mt-3 grid grid-cols-2 gap-3 text-xs">
                                <div>
                                    <p class="text-slate-400">Narx</p>
                                    <p class="mt-1 text-slate-700 dark:text-slate-200"><x-money :value="$item->unit_price" /></p>
                                </div>
                                <div>
                                    <p class="text-slate-400">Jami</p>
                                    <p class="mt-1 text-slate-700 dark:text-slate-200"><x-money :value="$item->line_total" /></p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 hidden overflow-x-auto lg:block">
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
                                <td class="py-3 pr-4">{{ number_format($item->quantity, 3) }} <x-unit-badge :value="$item->product?->unit ?? '-'" class="ml-1" /></td>
                                <td class="py-3 pr-4"><x-money :value="$item->unit_price" /></td>
                                <td class="py-3 font-semibold"><x-money :value="$item->line_total" /></td>
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
