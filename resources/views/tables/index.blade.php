<x-app-layout title="Zallar" pageTitle="Zallar" pageSubtitle="Zallar sig'imi, narxi va bandlik potentsialini premium ko'rinishda kuzating.">
    <div class="space-y-5">
        <x-admin.page-intro eyebrow="Toy boshqaruvi" title="Zallar" subtitle="Har bir zal bo'yicha sig'im, holat, tarif va bronlar sonini operatorga qulay formatda ko'rsating.">
            <x-slot:actions>
                <a href="{{ route('halls.create') }}" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    Zal qo'shish
                </a>
            </x-slot:actions>
        </x-admin.page-intro>

        <x-admin.filter-shell>
            <form method="GET" class="grid gap-3 md:grid-cols-[1.3fr_0.9fr_auto]">
                <input type="text" name="q" value="{{ $filters['search'] }}" placeholder="Zal nomi bo'yicha qidiring" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slate-400 dark:border-slate-700 dark:bg-slate-950">
                <select name="status" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slate-400 dark:border-slate-700 dark:bg-slate-950">
                    <option value="">Barcha holatlar</option>
                    @foreach(['Faol', 'Nofaol', "Ta'mirda"] as $status)
                        <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <button class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white dark:bg-white dark:text-slate-950">Filtrlash</button>
                    <a href="{{ route('halls.index') }}" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 dark:border-slate-700 dark:text-slate-300">Tozalash</a>
                </div>
            </form>
        </x-admin.filter-shell>

        @if($halls->count())
            <div class="grid gap-4 md:grid-cols-2 2xl:grid-cols-3">
                @foreach($halls as $hall)
                    <x-admin.section-card :title="$hall->name" :subtitle="$hall->description ?: 'Zal tavsifi kiritilmagan'" icon="building-2">
                        @if($hall->image_url)
                            <img src="{{ $hall->image_url }}" alt="{{ $hall->name }}" class="mb-4 h-48 w-full rounded-[24px] object-cover">
                        @endif
                        <div class="grid gap-3 sm:grid-cols-3">
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-800/70">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Sig'im</p>
                                <p class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ $hall->capacity }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-800/70">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Narx</p>
                                <p class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ number_format($hall->price, 0, '.', ' ') }} UZS</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-800/70">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Bronlar</p>
                                <p class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ $hall->bookings_count }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between gap-3 border-t border-slate-100 pt-4 dark:border-slate-800">
                            <x-status-badge :status="$hall->status" />
                            <div class="responsive-actions flex gap-2">
                                <x-action-link href="{{ route('halls.edit', $hall) }}" icon="pencil-line" variant="edit">Tahrirlash</x-action-link>
                                <form action="{{ route('halls.destroy', $hall) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <x-delete-button />
                                </form>
                            </div>
                        </div>
                    </x-admin.section-card>
                @endforeach
            </div>
        @else
            <x-admin.empty-state icon="building" title="Zallar topilmadi" text="Toyxona bo'yicha birinchi zalni qo'shing va bandlik monitoringini boshlang." action-href="{{ route('halls.create') }}" action-label="Zal qo'shish" />
        @endif

        <div>{{ $halls->links() }}</div>
    </div>
</x-app-layout>
