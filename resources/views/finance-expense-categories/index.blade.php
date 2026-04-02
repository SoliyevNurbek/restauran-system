<x-app-layout title="Xarajat kategoriyalari" pageTitle="Xarajat kategoriyalari">
    <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Kategoriyalar</h2>
            <p class="text-sm text-slate-500">Xarajatlarni tushunarli bo'limlar bilan ajrating</p>
        </div>
        <a href="{{ route('inventory-expense-categories.create') }}" class="rounded-2xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Kategoriya qo'shish</a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse($categories as $category)
            <div class="rounded-3xl bg-white p-5 shadow-soft dark:bg-slate-900">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ $category->name }}</h3>
                        <p class="mt-2 break-words text-sm text-slate-500">{{ $category->description ?: 'Izoh kiritilmagan.' }}</p>
                    </div>
                    <span class="shrink-0 rounded-full bg-gradient-to-r from-amber-500 to-orange-500 px-3 py-1 text-xs font-semibold text-white shadow-sm dark:from-amber-500 dark:to-orange-400">{{ $category->expenses_count }} ta</span>
                </div>
                <div class="responsive-actions mt-5 flex flex-col gap-2 sm:flex-row">
                    <x-action-link href="{{ route('inventory-expense-categories.edit', ['inventory_expense_category' => $category]) }}" icon="pencil-line" variant="edit">Tahrirlash</x-action-link>
                    <form method="POST" action="{{ route('inventory-expense-categories.destroy', ['inventory_expense_category' => $category]) }}">
                        @csrf
                        @method('DELETE')
                        <x-delete-button />
                    </form>
                </div>
            </div>
        @empty
            <div class="rounded-3xl bg-white p-8 text-sm text-slate-500 shadow-soft dark:bg-slate-900">Kategoriyalar hali yo'q.</div>
        @endforelse
    </div>

    <div class="mt-5">{{ $categories->links() }}</div>
</x-app-layout>
