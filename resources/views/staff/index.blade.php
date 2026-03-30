<x-app-layout title="Xodimlar" pageTitle="Xodimlar boshqaruvi">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold">Xodimlar</h2>
        <a href="{{ route('employees.create') }}" class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">Xodim qo'shish</a>
    </div>

    <div class="overflow-x-auto rounded-2xl bg-white shadow-soft dark:bg-slate-900">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left dark:bg-slate-800/70">
            <tr>
                <th class="px-4 py-3">Xodim</th>
                <th class="px-4 py-3">Telefon</th>
                <th class="px-4 py-3">Lavozim</th>
                <th class="px-4 py-3">Oylik</th>
                <th class="px-4 py-3">Holat</th>
                <th class="px-4 py-3 text-right">Amallar</th>
            </tr>
            </thead>
            <tbody>
            @forelse($employees as $employee)
                <tr class="border-t border-slate-100 dark:border-slate-800">
                    <td class="px-4 py-3 font-medium">{{ $employee->full_name }}</td>
                    <td class="px-4 py-3">{{ $employee->phone ?: '—' }}</td>
                    <td class="px-4 py-3"><x-status-badge :status="$employee->role" /></td>
                    <td class="px-4 py-3">{{ $employee->salary ? number_format($employee->salary, 0, '.', ' ').' so\'m' : '—' }}</td>
                    <td class="px-4 py-3"><x-status-badge :status="$employee->status" /></td>
                    <td class="px-4 py-3"><div class="flex justify-end gap-2"><a href="{{ route('employees.edit', $employee) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs dark:border-slate-700">Tahrirlash</a><form action="{{ route('employees.destroy', $employee) }}" method="POST">@csrf @method('DELETE')<x-delete-button /></form></div></td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-6 text-center text-slate-500">Xodimlar topilmadi.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $employees->links() }}</div>
</x-app-layout>

