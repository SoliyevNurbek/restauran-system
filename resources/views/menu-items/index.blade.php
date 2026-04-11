<x-app-layout title="To'y paketlari" pageTitle="To'y paketlari">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold">To'y paketlari</h2>
        <a href="{{ route('wedding-packages.create') }}" class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">Paket qo'shish</a>
    </div>

    <div class="mobile-fit-table overflow-x-auto rounded-2xl bg-white shadow-soft dark:bg-slate-900">
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
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                @if($package->image_url)
                                    <img src="{{ $package->image_url }}" class="h-10 w-10 rounded-lg object-cover" alt="paket">
                                @else
                                    <div class="h-10 w-10 rounded-lg bg-slate-200 dark:bg-slate-700"></div>
                                @endif
                                <div>
                                    <p class="font-medium">{{ $package->name }}</p>
                                    <p class="text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($package->description, 40) }}</p>
                                </div>
                            </div>

                            @if($package->images->isNotEmpty())
                                <div class="mobile-wrap-strip flex gap-2 overflow-x-auto pb-1">
                                    @foreach($package->images as $galleryImage)
                                        <img src="{{ $galleryImage->url() }}" class="h-16 w-24 shrink-0 rounded-xl object-cover" alt="{{ $package->name }}">
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3">{{ number_format($package->price_per_person, 0, '.', ' ') }} so'm</td>
                    <td class="px-4 py-3"><x-status-badge :status="$package->status" /></td>
                    <td class="px-4 py-3">
                        <div class="responsive-actions flex justify-end gap-2">
                            <x-action-link href="{{ route('wedding-packages.edit', $package) }}" icon="pencil-line" variant="edit">Tahrirlash</x-action-link>
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

