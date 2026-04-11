<x-app-layout title="Mijozlar" pageTitle="Mijozlar" pageSubtitle="Mijozlar bazasi, aloqa ma'lumotlari va bron tarixini tartibli ko'rinishda boshqaring.">
    <div class="space-y-5">
        <x-admin.page-intro eyebrow="Toy boshqaruvi" title="Mijozlar" subtitle="Doimiy mijozlar, yangi murojaatlar va upcoming bronlarni bir joydan kuzating.">
            <x-slot:actions>
                <a href="{{ route('clients.create') }}" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                    <i data-lucide="user-plus" class="h-4 w-4"></i>
                    Mijoz qo'shish
                </a>
            </x-slot:actions>
        </x-admin.page-intro>

        <x-admin.filter-shell>
            <form method="GET" class="flex flex-col gap-3 md:flex-row">
                <input type="text" name="q" value="{{ $filters['search'] }}" placeholder="Ism yoki telefon bo'yicha qidiring" class="flex-1 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-slate-400 dark:border-slate-700 dark:bg-slate-950">
                <div class="flex gap-2">
                    <button class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white dark:bg-white dark:text-slate-950">Filtrlash</button>
                    <a href="{{ route('clients.index') }}" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 dark:border-slate-700 dark:text-slate-300">Tozalash</a>
                </div>
            </form>
        </x-admin.filter-shell>

        @if($clients->count())
            <div class="grid gap-4 xl:grid-cols-2">
                @foreach($clients as $client)
                    @php($latestBooking = $client->bookings->first())
                    <x-admin.section-card icon="users" :title="$client->full_name" :subtitle="$client->phone ?: 'Telefon ko‘rsatilmagan'">
                        <div class="grid gap-3 sm:grid-cols-3">
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-800/70">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Bronlar</p>
                                <p class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">{{ $client->bookings_count }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-800/70">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Oxirgi tadbir</p>
                                <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">{{ $latestBooking?->eventType?->name ?? "Yo'q" }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-800/70">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Yaqin bron</p>
                                <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">{{ optional($latestBooking?->event_date)->format('d.m.Y') ?? "Yo'q" }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4 dark:border-slate-800">
                            <div class="text-sm text-slate-500">
                                {{ $client->passport_info ?: "Pasport ma'lumoti kiritilmagan" }}
                            </div>
                            <div class="responsive-actions flex gap-2">
                                <x-action-link href="{{ route('clients.show', $client) }}" icon="eye" variant="view">Ko'rish</x-action-link>
                                <x-action-link href="{{ route('clients.edit', $client) }}" icon="pencil-line" variant="edit">Tahrirlash</x-action-link>
                                <form action="{{ route('clients.destroy', $client) }}" method="POST">
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
            <x-admin.empty-state icon="users" title="Mijozlar topilmadi" text="Mijoz bazasi hali shakllanmagan. Birinchi mijozni qo'shib bronlar tarixini yuriting." action-href="{{ route('clients.create') }}" action-label="Mijoz qo'shish" />
        @endif

        <div>{{ $clients->links() }}</div>
    </div>
</x-app-layout>
