<x-app-layout title="Mahsulotlar" pageTitle="Mahsulotlar" pageSubtitle="Ombordagi mahsulotlar, minimal limitlar va holatlarni qulay boshqaruv jadvalida kuzating.">
    <div class="space-y-5">
        <x-admin.page-intro eyebrow="Ombor" title="Mahsulotlar" subtitle="Mahsulotlar, SKU, kategoriya va qoldiq kesimini operatorlar uchun tez ishlaydigan formatda ko'ring.">
            <x-slot:actions>
                <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    Mahsulot qo'shish
                </a>
            </x-slot:actions>
        </x-admin.page-intro>

        <x-admin.filter-shell>
            <form method="GET" class="grid gap-3 md:grid-cols-[1.5fr_0.9fr_auto]">
                <input type="text" name="q" value="{{ $filters['search'] }}" placeholder="Mahsulot, SKU yoki kategoriya bo'yicha qidiring" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slate-400 dark:border-slate-700 dark:bg-slate-950">
                <select name="stock_state" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slate-400 dark:border-slate-700 dark:bg-slate-950">
                    <option value="">Barcha holatlar</option>
                    <option value="low" @selected($filters['stockState'] === 'low')>Kam qoldiq</option>
                    <option value="active" @selected($filters['stockState'] === 'active')>Faol</option>
                    <option value="inactive" @selected($filters['stockState'] === 'inactive')>Faol emas</option>
                </select>
                <div class="flex gap-2">
                    <button class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white dark:bg-white dark:text-slate-950">Filtrlash</button>
                    <a href="{{ route('products.index') }}" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 dark:border-slate-700 dark:text-slate-300">Tozalash</a>
                </div>
            </form>
        </x-admin.filter-shell>

        @if($products->count())
            <div class="overflow-hidden rounded-[30px] border border-slate-200/80 bg-white shadow-soft dark:border-slate-800 dark:bg-slate-900">
                <div class="mobile-fit-table overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50/90 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400 dark:bg-slate-950/70">
                            <tr>
                                <th class="px-5 py-4">Mahsulot</th>
                                <th class="px-5 py-4">Kategoriya</th>
                                <th class="px-5 py-4">Qoldiq</th>
                                <th class="px-5 py-4">Narx</th>
                                <th class="px-5 py-4">Holat</th>
                                <th class="px-5 py-4 text-right">Amallar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($products as $product)
                                @php
                                    $stockBadge = ! $product->is_active ? 'Faol emas' : ((float) $product->current_stock <= (float) $product->minimum_stock ? 'Kam qoldi' : 'Yetarli');
                                @endphp
                                <tr class="transition hover:bg-slate-50/70 dark:hover:bg-slate-950/40">
                                    <td class="px-5 py-4">
                                        <p class="font-semibold text-slate-900 dark:text-white">{{ $product->name }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $product->sku }} · {{ $product->subcategory }}</p>
                                    </td>
                                    <td class="px-5 py-4">{{ $product->category }}</td>
                                    <td class="px-5 py-4">
                                        <p class="font-semibold text-slate-900 dark:text-white">{{ number_format($product->current_stock, 3) }} {{ $product->unit }}</p>
                                        <p class="mt-1 text-xs text-slate-500">Minimal: {{ number_format($product->minimum_stock, 3) }}</p>
                                    </td>
                                    <td class="px-5 py-4">{{ number_format($product->last_purchase_price, 0, '.', ' ') }} UZS</td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $stockBadge === 'Faol emas' ? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' : ($stockBadge === 'Kam qoldi' ? 'bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300') }}">{{ $stockBadge }}</span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="responsive-actions flex justify-end gap-2">
                                            <x-action-link href="{{ route('products.edit', $product) }}" icon="pencil-line" variant="edit">Tahrirlash</x-action-link>
                                            <form method="POST" action="{{ route('products.destroy', $product) }}">
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
            <x-admin.empty-state icon="package-search" title="Mahsulotlar topilmadi" text="Mahsulot ombori hali shakllanmagan. Birinchi pozitsiyani qo'shib ombor nazoratini boshlang." action-href="{{ route('products.create') }}" action-label="Mahsulot qo'shish" />
        @endif

        <div>{{ $products->links() }}</div>
    </div>
</x-app-layout>
