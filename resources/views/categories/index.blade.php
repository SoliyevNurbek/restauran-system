<x-app-layout title="Tadbir turlari" pageTitle="Tadbirlar">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold">Tadbir turlari</h2>
        <a href="{{ route('event-types.create') }}" class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">Tadbir turi qo'shish</a>
    </div>

    <div class="mobile-fit-table overflow-x-auto rounded-2xl bg-white shadow-soft dark:bg-slate-900">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left dark:bg-slate-800/70">
            <tr>
                <th class="px-4 py-3">Nomi</th>
                <th class="px-4 py-3">Tavsif</th>
                <th class="px-4 py-3 text-right">Amallar</th>
            </tr>
            </thead>
            <tbody>
            @forelse($eventTypes as $eventType)
                <tr class="border-t border-slate-100 dark:border-slate-800">
                    <td class="px-4 py-3 font-medium">{{ $eventType->name }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $eventType->description ?: '-' }}</td>
                    <td class="px-4 py-3">
                        <div class="responsive-actions flex justify-end gap-2">
                            <x-action-link href="{{ route('event-types.edit', $eventType) }}" icon="pencil-line" variant="edit">Tahrirlash</x-action-link>
                            <form action="{{ route('event-types.destroy', $eventType) }}" method="POST">
                                @csrf @method('DELETE')
                                <x-delete-button />
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="px-4 py-6 text-center text-slate-500">Tadbir turlari topilmadi.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $eventTypes->links() }}</div>
</x-app-layout>

