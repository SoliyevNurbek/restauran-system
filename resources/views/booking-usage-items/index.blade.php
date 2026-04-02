<x-app-layout title="Bron mahsulot sarfi" pageTitle="Bron mahsulot sarfi">
    <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Bron mahsulot sarfi</h2>
            <p class="text-sm text-slate-500">Qaysi toyda qaysi mahsulotdan qancha ishlatilgani shu yerda yuritiladi</p>
        </div>
        <a href="{{ route('booking-usage-items.create') }}" class="rounded-2xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Usage qo'shish</a>
    </div>

    <div class="space-y-3 lg:hidden">
        @forelse($items as $item)
            <div class="rounded-3xl bg-white p-4 shadow-soft dark:bg-slate-900">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $item->name }}</p>
                        <p class="mt-1 text-xs text-slate-500">Bron: {{ $item->booking?->booking_number }}</p>
                    </div>
                    <div class="rounded-2xl bg-primary-50 px-3 py-1.5 text-xs font-semibold text-primary-700 dark:bg-primary-950/30 dark:text-primary-300">
                        {{ number_format($item->quantity, 3) }} {{ $item->unit }}
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 text-xs">
                    <div>
                        <p class="text-slate-400">SKU</p>
                        <p class="mt-1 break-all text-slate-700 dark:text-slate-200">{{ $item->sku }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400">Category</p>
                        <p class="mt-1 break-words text-slate-700 dark:text-slate-200">{{ $item->category }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-slate-400">Subcategory</p>
                        <p class="mt-1 break-words text-slate-700 dark:text-slate-200">{{ $item->subcategory }}</p>
                    </div>
                </div>

                <div class="responsive-actions mt-4 flex flex-wrap gap-2">
                    <x-action-link href="{{ route('booking-usage-items.edit', $item) }}" icon="pencil-line" variant="edit">Tahrirlash</x-action-link>
                    <form method="POST" action="{{ route('booking-usage-items.destroy', $item) }}">
                        @csrf
                        @method('DELETE')
                        <x-delete-button />
                    </form>
                </div>
            </div>
        @empty
            <div class="rounded-3xl bg-white p-8 text-center text-sm text-slate-500 shadow-soft dark:bg-slate-900">Booking usage items hali yo'q.</div>
        @endforelse
    </div>

    <div class="hidden overflow-hidden rounded-3xl bg-white shadow-soft dark:bg-slate-900 lg:block">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 dark:bg-slate-800/70 dark:text-slate-300">
                <tr>
                    <th class="px-5 py-4">Booking</th>
                    <th class="px-5 py-4">SKU</th>
                    <th class="px-5 py-4">Mahsulot</th>
                    <th class="px-5 py-4">Category</th>
                    <th class="px-5 py-4">Subcategory</th>
                    <th class="px-5 py-4">Miqdor</th>
                    <th class="px-5 py-4">Amallar</th>
                </tr>
                </thead>
                <tbody>
                @forelse($items as $item)
                    <tr class="border-t border-slate-100 dark:border-slate-800">
                        <td class="px-5 py-4">{{ $item->booking?->booking_number }}</td>
                        <td class="px-5 py-4">{{ $item->sku }}</td>
                        <td class="px-5 py-4 font-medium text-slate-900 dark:text-white">{{ $item->name }}</td>
                        <td class="px-5 py-4">{{ $item->category }}</td>
                        <td class="px-5 py-4">{{ $item->subcategory }}</td>
                        <td class="px-5 py-4">{{ number_format($item->quantity, 3) }} <x-unit-badge :value="$item->unit" class="ml-1" /></td>
                        <td class="px-5 py-4">
                            <div class="responsive-actions flex flex-wrap gap-2">
                                <x-action-link href="{{ route('booking-usage-items.edit', $item) }}" icon="pencil-line" variant="edit">Tahrirlash</x-action-link>
                                <form method="POST" action="{{ route('booking-usage-items.destroy', $item) }}">
                                    @csrf
                                    @method('DELETE')
                                    <x-delete-button />
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-8 text-center text-sm text-slate-500">Booking usage items hali yo'q.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">{{ $items->links() }}</div>
</x-app-layout>
