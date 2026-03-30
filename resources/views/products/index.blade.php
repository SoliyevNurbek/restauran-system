<x-app-layout title="Mahsulotlar" pageTitle="Mahsulotlar">
    <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Mahsulotlar ombori</h2>
            <p class="text-sm text-slate-500">Qoldiq, minimal limit va oxirgi xarid narxi bilan boshqaruv</p>
        </div>
        <a href="{{ route('products.create') }}" class="rounded-2xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Mahsulot qo'shish</a>
    </div>

    <div class="overflow-hidden rounded-3xl bg-white shadow-soft dark:bg-slate-900">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 dark:bg-slate-800/70 dark:text-slate-300">
                <tr>
                    <th class="px-5 py-4">Mahsulot</th>
                    <th class="px-5 py-4">Birlik</th>
                    <th class="px-5 py-4">Qoldiq</th>
                    <th class="px-5 py-4">Minimal</th>
                    <th class="px-5 py-4">Oxirgi narx</th>
                    <th class="px-5 py-4">Holati</th>
                    <th class="px-5 py-4">Amallar</th>
                </tr>
                </thead>
                <tbody>
                @forelse($products as $product)
                    <tr class="border-t border-slate-100 dark:border-slate-800">
                        <td class="px-5 py-4 font-medium text-slate-900 dark:text-white">{{ $product->name }}</td>
                        <td class="px-5 py-4">{{ $product->unit }}</td>
                        <td class="px-5 py-4">{{ number_format($product->current_stock, 3) }}</td>
                        <td class="px-5 py-4">{{ number_format($product->minimum_stock, 3) }}</td>
                        <td class="px-5 py-4">{{ number_format($product->last_purchase_price, 2) }}</td>
                        <td class="px-5 py-4">
                            @if(!$product->is_active)
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-300">Faol emas</span>
                            @elseif($product->current_stock <= $product->minimum_stock)
                                <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700 dark:bg-amber-950/40 dark:text-amber-300">Kam qoldi</span>
                            @else
                                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">Yetarli</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('products.edit', $product) }}" class="rounded-xl bg-slate-900 px-3 py-1.5 text-xs font-medium text-white dark:bg-slate-100 dark:text-slate-900">Tahrirlash</a>
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
                        <td colspan="7" class="px-5 py-8 text-center text-sm text-slate-500">Mahsulotlar hali kiritilmagan.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">{{ $products->links() }}</div>
</x-app-layout>
