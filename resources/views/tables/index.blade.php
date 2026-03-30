<x-app-layout title="Stollar" pageTitle="Stol boshqaruvi">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold">Ovqatlanish stollari</h2>
        <a href="{{ route('tables.create') }}" class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">Stol qo'shish</a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @forelse($tables as $table)
            <div class="rounded-2xl bg-white p-5 shadow-soft dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold">Stol {{ $table->table_number }}</h3>
                    <x-status-badge :status="$table->status" />
                </div>
                <div class="mt-4 flex gap-2">
                    <a href="{{ route('tables.edit', $table) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800">Tahrirlash</a>
                    <form action="{{ route('tables.destroy', $table) }}" method="POST">@csrf @method('DELETE')<x-delete-button /></form>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-500">Stollar topilmadi.</p>
        @endforelse
    </div>

    <div class="mt-4">{{ $tables->links() }}</div>
</x-app-layout>
