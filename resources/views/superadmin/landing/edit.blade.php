<x-layouts.superadmin :title="$pageTitle" :page-title="$pageTitle">
    <div class="mb-4 flex items-center gap-3">
        @foreach(['uz' => 'UZ', 'uzc' => 'РЈР-', 'ru' => 'RU', 'en' => 'EN'] as $lang => $label)
            <a href="{{ route('superadmin.landing.edit', ['lang' => $lang]) }}" class="rounded-xl px-4 py-2 text-sm font-semibold {{ $locale === $lang ? 'bg-indigo-600 text-white' : 'border border-slate-200 bg-white text-slate-700' }}">{{ $label }}</a>
        @endforeach
    </div>
    <form method="POST" action="{{ route('superadmin.landing.update') }}" class="rounded-3xl border border-slate-200 bg-white p-6">
        @csrf
        @method('PUT')
        <input type="hidden" name="locale" value="{{ $locale }}">
        <div class="grid gap-4 md:grid-cols-2">
            <div class="md:col-span-2"><label class="mb-2 block text-sm font-medium text-slate-700">Hero badge</label><input name="hero_badge" value="{{ old('hero_badge', $content->hero_badge) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3"></div>
            <div class="md:col-span-2"><label class="mb-2 block text-sm font-medium text-slate-700">Hero title</label><input name="hero_title" value="{{ old('hero_title', $content->hero_title) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3"></div>
            <div class="md:col-span-2"><label class="mb-2 block text-sm font-medium text-slate-700">Hero text</label><textarea name="hero_text" rows="3" class="w-full rounded-2xl border border-slate-200 px-4 py-3">{{ old('hero_text', $content->hero_text) }}</textarea></div>
            <div><label class="mb-2 block text-sm font-medium text-slate-700">Primary CTA</label><input name="hero_primary_cta" value="{{ old('hero_primary_cta', $content->hero_primary_cta) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3"></div>
            <div><label class="mb-2 block text-sm font-medium text-slate-700">Secondary CTA</label><input name="hero_secondary_cta" value="{{ old('hero_secondary_cta', $content->hero_secondary_cta) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3"></div>
            <div class="md:col-span-2"><label class="mb-2 block text-sm font-medium text-slate-700">Hero microcopy</label><input name="hero_microcopy" value="{{ old('hero_microcopy', $content->hero_microcopy) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3"></div>
            <div class="md:col-span-2"><label class="mb-2 block text-sm font-medium text-slate-700">Final title</label><input name="final_title" value="{{ old('final_title', $content->final_title) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3"></div>
            <div class="md:col-span-2"><label class="mb-2 block text-sm font-medium text-slate-700">Final text</label><textarea name="final_text" rows="3" class="w-full rounded-2xl border border-slate-200 px-4 py-3">{{ old('final_text', $content->final_text) }}</textarea></div>
            <div><label class="mb-2 block text-sm font-medium text-slate-700">Contact title</label><input name="contact_title" value="{{ old('contact_title', $content->contact_title) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3"></div>
            <div><label class="mb-2 block text-sm font-medium text-slate-700">Contact text</label><input name="contact_text" value="{{ old('contact_text', $content->contact_text) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3"></div>
        </div>
        <button type="submit" class="mt-6 rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white">Landingni saqlash</button>
    </form>
</x-layouts.superadmin>
