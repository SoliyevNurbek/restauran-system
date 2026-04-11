<x-layouts.superadmin :title="$pageTitle" :page-title="$pageTitle" page-subtitle="Tarjima matnlari, completeness hissi va platforma copy boshqaruvi.">
    <div class="flex flex-wrap gap-2">
        @foreach (['uz' => "O'zbekcha", 'uzc' => 'Ўзбекча', 'ru' => 'Русский', 'en' => 'English'] as $lang => $label)
            <a href="{{ route('superadmin.languages.edit', ['lang' => $lang]) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold {{ $locale === $lang ? 'bg-slate-950 text-white' : 'border border-slate-200 bg-white text-slate-700' }}">{{ $label }}</a>
        @endforeach
    </div>

    <x-superadmin.panel class="mt-6" title="Translation workspace" subtitle="Muhim landing va auth copy'larni shu yerda yangilang." icon="languages">
        <form method="POST" action="{{ route('superadmin.languages.update') }}" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="locale" value="{{ $locale }}">
            <div class="grid gap-4 md:grid-cols-2">
                @foreach($fields as $key => $label)
                    <div class="rounded-[24px] border border-slate-200 p-4">
                        <label class="mb-2 block text-sm font-semibold text-slate-900">{{ $label }}</label>
                        <textarea name="lines[{{ $key }}]" rows="3" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">{{ old("lines.$key", $lines[$key] ?? '') }}</textarea>
                        <p class="mt-2 text-xs text-slate-400">{{ $key }}</p>
                    </div>
                @endforeach
            </div>
            <button class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Tarjimalarni saqlash</button>
        </form>
    </x-superadmin.panel>
</x-layouts.superadmin>
