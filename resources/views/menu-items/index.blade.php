<x-app-layout title="Menyu" pageTitle="Menyu boshqaruvi">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold">Taomlar</h2>
        <a href="{{ route('menu-items.create') }}" class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">Taom qo'shish</a>
    </div>

    <div class="overflow-x-auto rounded-2xl bg-white shadow-soft dark:bg-slate-900">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left dark:bg-slate-800/70">
            <tr>
                <th class="px-4 py-3">Taom</th>
                <th class="px-4 py-3">Kategoriya</th>
                <th class="px-4 py-3">Narx</th>
                <th class="px-4 py-3">Holat</th>
                <th class="px-4 py-3 text-right">Amallar</th>
            </tr>
            </thead>
            <tbody>
            @forelse($menuItems as $item)
                <tr class="border-t border-slate-100 dark:border-slate-800">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            @if($item->image_path)
                                <img src="{{ asset('storage/'.$item->image_path) }}" class="h-10 w-10 rounded-lg object-cover" alt="taom">
                            @else
                                <div class="h-10 w-10 rounded-lg bg-slate-200 dark:bg-slate-700"></div>
                            @endif
                            <div>
                                <p class="font-medium">{{ $item->name }}</p>
                                <p class="text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($item->description, 40) }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">{{ $item->category->name }}</td>
                    <td class="px-4 py-3">${{ number_format($item->price, 2) }}</td>
                    <td class="px-4 py-3"><x-status-badge :status="$item->status" /></td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('menu-items.edit', $item) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800">Tahrirlash</a>
                            <form action="{{ route('menu-items.destroy', $item) }}" method="POST">@csrf @method('DELETE')<x-delete-button /></form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-6 text-center text-slate-500">Menyu elementlari topilmadi.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $menuItems->links() }}</div>
</x-app-layout>
