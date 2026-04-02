<x-app-layout title="Asosiy mahsulotlar" pageTitle="Asosiy mahsulotlar">
    <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Asosiy mahsulotlar</h2>
            <p class="text-sm text-slate-500">Umumiy katalog: mahsulotlar, kategoriyalar va qoldiq nazorati</p>
        </div>
        <a href="{{ route('products.create') }}" class="rounded-2xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Mahsulot qo'shish</a>
    </div>

    <div class="space-y-3 lg:hidden">
        @forelse($products as $product)
            <div class="rounded-3xl bg-white p-4 shadow-soft dark:bg-slate-900">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $product->name }}</p>
                        <p class="mt-1 break-all text-xs text-slate-500">{{ $product->sku }}</p>
                    </div>
                    @if(!$product->is_active)
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-300">Faol emas</span>
                    @elseif($product->current_stock <= $product->minimum_stock)
                        <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-medium text-amber-700 dark:bg-amber-950/40 dark:text-amber-300">Kam</span>
                    @else
                        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-medium text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">Yetarli</span>
                    @endif
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 text-xs">
                    <div>
                        <p class="text-slate-400">Category</p>
                        <p class="mt-1 break-words text-slate-700 dark:text-slate-200">{{ $product->category }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400">Subcategory</p>
                        <div class="mt-1 flex flex-wrap items-center gap-1.5 text-slate-700 dark:text-slate-200">
                            <span class="break-words">{{ $product->subcategory }}</span>
                            <x-unit-badge :value="$product->unit" />
                        </div>
                    </div>
                    <div>
                        <p class="text-slate-400">Qoldiq</p>
                        <p class="mt-1 text-slate-700 dark:text-slate-200">{{ number_format($product->current_stock, 3) }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400">Minimal</p>
                        <p class="mt-1 text-slate-700 dark:text-slate-200">{{ number_format($product->minimum_stock, 3) }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-slate-400">Oxirgi narx, so'm</p>
                        <p class="mt-1 text-slate-700 dark:text-slate-200"><x-money :value="$product->last_purchase_price" :showSuffix="false" /></p>
                    </div>
                </div>

                <div class="responsive-actions mt-4 flex flex-wrap gap-2">
                    <x-action-link href="{{ route('products.edit', $product) }}" icon="pencil-line" variant="edit">Tahrirlash</x-action-link>
                    <form method="POST" action="{{ route('products.destroy', $product) }}">
                        @csrf
                        @method('DELETE')
                        <x-delete-button />
                    </form>
                </div>
            </div>
        @empty
            <div class="rounded-3xl bg-white px-5 py-8 text-center text-sm text-slate-500 shadow-soft dark:bg-slate-900">Mahsulotlar hali kiritilmagan.</div>
        @endforelse
    </div>

    <div class="hidden overflow-hidden rounded-3xl bg-white shadow-soft dark:bg-slate-900 lg:block">
        <div class="max-h-[calc(100vh-16rem)] overflow-y-auto">
            <table class="w-full table-fixed text-left text-xs xl:text-sm">
                <thead class="bg-slate-50 text-slate-500 dark:bg-slate-800/70 dark:text-slate-300">
                <tr>
                    <th class="w-[18%] px-3 py-2.5">Mahsulot</th>
                    <th class="w-[11%] px-3 py-2.5">SKU</th>
                    <th class="w-[13%] px-3 py-2.5">Category</th>
                    <th class="w-[16%] px-3 py-2.5">Subcategory</th>
                    <th class="w-[10%] px-3 py-2.5">Qoldiq</th>
                    <th class="w-[10%] px-3 py-2.5">Minimal</th>
                    <th class="w-[10%] px-3 py-2.5">Oxirgi narx, so'm</th>
                    <th class="w-[8%] px-3 py-2.5">Holati</th>
                    <th class="w-[14%] px-3 py-2.5">Amallar</th>
                </tr>
                </thead>
                <tbody>
                @forelse($products as $product)
                    <tr class="border-t border-slate-100 dark:border-slate-800">
                        <td class="px-3 py-2.5 font-medium text-slate-900 dark:text-white">
                            <div class="line-clamp-2 break-words">{{ $product->name }}</div>
                        </td>
                        <td class="px-3 py-2.5 break-all text-slate-500">{{ $product->sku }}</td>
                        <td class="px-3 py-2.5 break-words">{{ $product->category }}</td>
                        <td class="px-3 py-2.5">
                            <div class="flex flex-wrap items-center gap-1.5">
                                <span class="break-words">{{ $product->subcategory }}</span>
                                <x-unit-badge :value="$product->unit" />
                            </div>
                        </td>
                        <td class="px-3 py-2.5 whitespace-nowrap">{{ number_format($product->current_stock, 3) }}</td>
                        <td class="px-3 py-2.5 whitespace-nowrap">{{ number_format($product->minimum_stock, 3) }}</td>
                        <td class="px-3 py-2.5 whitespace-nowrap"><x-money :value="$product->last_purchase_price" :showSuffix="false" /></td>
                        <td class="px-3 py-2.5">
                            @if(!$product->is_active)
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-300">Faol emas</span>
                            @elseif($product->current_stock <= $product->minimum_stock)
                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-medium text-amber-700 dark:bg-amber-950/40 dark:text-amber-300">Kam</span>
                            @else
                                <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-medium text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">Yetarli</span>
                            @endif
                        </td>
                        <td class="px-3 py-2.5">
                            <div class="flex flex-wrap gap-1.5">
                                <x-action-link href="{{ route('products.edit', $product) }}" icon="pencil-line" variant="edit">Tahrirlash</x-action-link>
                                <form method="POST" action="{{ route('products.destroy', $product) }}">
                                    @csrf
                                    @method('DELETE')
                                    <x-delete-button />
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-5 py-8 text-center text-sm text-slate-500">Mahsulotlar hali kiritilmagan.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
