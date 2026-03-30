<x-app-layout title="Xodimlar" pageTitle="Xodimlar boshqaruvi">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold">Xodimlar</h2>
        <a href="{{ route('staff.create') }}" class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">Xodim qo'shish</a>
    </div>

    <div class="overflow-x-auto rounded-2xl bg-white shadow-soft dark:bg-slate-900">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left dark:bg-slate-800/70"><tr><th class="px-4 py-3">Nomi</th><th class="px-4 py-3">Elektron pochta</th><th class="px-4 py-3">Telefon</th><th class="px-4 py-3">Lavozim</th><th class="px-4 py-3 text-right">Amallar</th></tr></thead>
            <tbody>
            @forelse($staffMembers as $member)
                <tr class="border-t border-slate-100 dark:border-slate-800">
                    <td class="px-4 py-3 font-medium">{{ $member->name }}</td>
                    <td class="px-4 py-3">{{ $member->email ?: '—' }}</td>
                    <td class="px-4 py-3">{{ $member->phone ?: '—' }}</td>
                    <td class="px-4 py-3"><x-status-badge :status="$member->role" /></td>
                    <td class="px-4 py-3"><div class="flex justify-end gap-2"><a href="{{ route('staff.edit', $member) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs dark:border-slate-700">Tahrirlash</a><form action="{{ route('staff.destroy', $member) }}" method="POST">@csrf @method('DELETE')<x-delete-button /></form></div></td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-6 text-center text-slate-500">Xodimlar topilmadi.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $staffMembers->links() }}</div>
</x-app-layout>
