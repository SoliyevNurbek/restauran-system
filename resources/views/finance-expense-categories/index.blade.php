<x-app-layout title="Xarajat kategoriyalari" pageTitle="Xarajat kategoriyalari">
    <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Kategoriyalar</h2>
            <p class="text-sm text-slate-500">Xarajatlarni tushunarli bo'limlar bilan ajrating</p>
        </div>
        <a href="{{ route('inventory-expense-categories.create') }}" class="rounded-2xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Kategoriya qo'shish</a>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse($categories as $category)
            <div class="rounded-3xl bg-white p-5 shadow-soft dark:bg-slate-900">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ $category->name }}</h3>
                        <p class="mt-2 text-sm text-slate-500">{{ $category->description ?: 'Izoh kiritilmagan.' }}</p>
                    </div>
                    <span class="rounded-full bg-primary-50 px-2.5 py-1 text-xs font-medium text-primary-700 dark:bg-primary-950/40 dark:text-primary-300">{{ $category->expenses_count }} ta</span>
                </div>
                <div class="mt-5 flex gap-2">
                    <a href="{{ route('inventory-expense-categories.edit', $category) }}" class="rounded-xl bg-slate-900 px-3 py-1.5 text-xs font-medium text-white dark:bg-slate-100 dark:text-slate-900">Tahrirlash</a>
                    <form method="POST" action="{{ route('inventory-expense-categories.destroy', $category) }}">
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
