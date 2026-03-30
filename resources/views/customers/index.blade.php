<x-app-layout title="Mijozlar" pageTitle="Mijozlar">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold">Mijozlar</h2>
        <a href="{{ route('clients.create') }}" class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">Mijoz qo'shish</a>
    </div>

    <div class="overflow-x-auto rounded-2xl bg-white shadow-soft dark:bg-slate-900">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left dark:bg-slate-800/70">
            <tr>
                <th class="px-4 py-3">Mijoz</th>
                <th class="px-4 py-3">Telefon</th>
                <th class="px-4 py-3">Bronlar</th>
                <th class="px-4 py-3 text-right">Amallar</th>
            </tr>
            </thead>
            <tbody>
            @forelse($clients as $client)
                <tr class="border-t border-slate-100 dark:border-slate-800">
                    <td class="px-4 py-3">
                        <p class="font-medium">{{ $client->full_name }}</p>
                        <p class="text-xs text-slate-500">{{ $client->passport_info ?: 'Pasport ma\'lumoti yo\'q' }}</p>
                    </td>
                    <td class="px-4 py-3">{{ $client->phone ?: '—' }}</td>
                    <td class="px-4 py-3">{{ $client->bookings_count }}</td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('clients.show', $client) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs dark:border-slate-700">Ko'rish</a>
                            <a href="{{ route('clients.edit', $client) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs dark:border-slate-700">Tahrirlash</a>
                            <form action="{{ route('clients.destroy', $client) }}" method="POST">@csrf @method('DELETE')<x-delete-button /></form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-6 text-center text-slate-500">Mijozlar topilmadi.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $clients->links() }}</div>
</x-app-layout>

