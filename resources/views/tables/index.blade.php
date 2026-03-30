<x-app-layout title="Zallar" pageTitle="Zallar">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold">Zallar ro'yxati</h2>
        <a href="{{ route('halls.create') }}" class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700">Zal qo'shish</a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @forelse($halls as $hall)
            <div class="rounded-2xl bg-white p-5 shadow-soft dark:bg-slate-900">
                @if($hall->image)
                    <img src="{{ asset('storage/'.$hall->image) }}" alt="{{ $hall->name }}" class="mb-4 h-36 w-full rounded-xl object-cover">
                @endif
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold">{{ $hall->name }}</h3>
                    <x-status-badge :status="$hall->status" />
                </div>
                <p class="mt-2 text-sm text-slate-500">{{ $hall->capacity }} mehmon • {{ number_format($hall->price, 0, '.', ' ') }} so'm</p>
                <p class="mt-1 text-xs text-slate-400">{{ \Illuminate\Support\Str::limit($hall->description, 70) }}</p>
                <div class="mt-4 flex gap-2">
                    <a href="{{ route('halls.edit', $hall) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800">Tahrirlash</a>
                    <form action="{{ route('halls.destroy', $hall) }}" method="POST">@csrf @method('DELETE')<x-delete-button /></form>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-500">Zallar topilmadi.</p>
        @endforelse
    </div>

    <div class="mt-4">{{ $halls->links() }}</div>
</x-app-layout>

