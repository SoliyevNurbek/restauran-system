<x-layouts.superadmin :title="$pageTitle" :page-title="$pageTitle" page-subtitle="Huquqiy sahifalar, versiyalar va nashr sanalari boshqaruvi.">
    <div class="flex flex-wrap gap-2">
        @foreach($pages as $pageSlug => $meta)
            <a href="{{ route('superadmin.pages.edit', ['slug' => $pageSlug]) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold {{ $slug === $pageSlug ? 'bg-slate-950 text-white' : 'border border-slate-200 bg-white text-slate-700' }}">
                {{ $meta['label'] }}
            </a>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.15fr_.85fr]">
        <x-superadmin.panel title="{{ $pages[$slug]['label'] }}" :subtitle="$pages[$slug]['description']" icon="file-text">
            <form method="POST" action="{{ route('superadmin.pages.update') }}" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="slug" value="{{ $slug }}">
                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Sarlavha</label>
                    <input type="text" name="title" value="{{ old('title', $currentPage?->title) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                </div>
                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Kontent</label>
                    <textarea name="content" rows="18" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">{{ old('content', $currentPage?->content) }}</textarea>
                </div>
                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Nashr sanasi</label>
                    <input type="datetime-local" name="published_at" value="{{ old('published_at', optional($currentPage?->published_at)->format('Y-m-d\TH:i')) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                </div>
                <button class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Versiyani saqlash</button>
            </form>
        </x-superadmin.panel>

        <x-superadmin.panel title="Version history" subtitle="Published document versiyalari." icon="history">
            @forelse($history as $version)
                <div class="rounded-2xl border border-slate-200 px-4 py-4 {{ !$loop->last ? 'mb-3' : '' }}">
                    <div class="flex items-center justify-between gap-3">
                        <p class="font-semibold text-slate-900">v{{ $version->version }}</p>
                        <p class="text-xs text-slate-400">{{ optional($version->published_at)->format('d.m.Y H:i') }}</p>
                    </div>
                    <p class="mt-2 text-sm text-slate-500">{{ $version->title }}</p>
                </div>
            @empty
                <x-superadmin.empty-state icon="file-clock" title="Versiyalar hali yo'q" description="Sahifa saqlangach version history shu yerda paydo bo'ladi." />
            @endforelse
        </x-superadmin.panel>
    </div>
</x-layouts.superadmin>
