<x-app-layout title="To'y paketlari" pageTitle="Toy paketlari">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold">To'y paketlari</h2>
        <a href="{{ route('wedding-packages.create') }}" class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">Paket qo'shish</a>
    </div>

    <div class="overflow-x-auto rounded-2xl bg-white shadow-soft dark:bg-slate-900">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left dark:bg-slate-800/70">
            <tr>
                <th class="px-4 py-3">Paket</th>
                <th class="px-4 py-3">Bir kishilik narx</th>
                <th class="px-4 py-3">Holat</th>
                <th class="px-4 py-3 text-right">Amallar</th>
            </tr>
            </thead>
            <tbody>
            @forelse($packages as $package)
                <tr class="border-t border-slate-100 dark:border-slate-800">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            @if($package->image)
                                <img src="{{ asset('storage/'.$package->image) }}" class="h-10 w-10 rounded-lg object-cover" alt="paket">
                            @else
                                <div class="h-10 w-10 rounded-lg bg-slate-200 dark:bg-slate-700"></div>
                            @endif
                            <div>
                                <p class="font-medium">{{ $package->name }}</p>
                                <p class="text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($package->description, 40) }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">{{ number_format($package->price_per_person, 0, '.', ' ') }} so'm</td>
                    <td class="px-4 py-3"><x-status-badge :status="$package->status" /></td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('wedding-packages.edit', $package) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800">Tahrirlash</a>
                            <form action="{{ route('wedding-packages.destroy', $package) }}" method="POST">@csrf @method('DELETE')<x-delete-button /></form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-6 text-center text-slate-500">To'y paketlari topilmadi.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $packages->links() }}</div>
</x-app-layout>

