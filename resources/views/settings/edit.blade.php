<x-app-layout title="Sozlamalar" pageTitle="Sozlamalar">
    <div class="grid gap-6 xl:grid-cols-3">
        <div class="rounded-3xl bg-white p-6 shadow-soft dark:bg-slate-900 xl:col-span-2">
            <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="grid gap-4 md:grid-cols-2" data-loading-form>
                @csrf
                @method('PUT')

                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium">Tizim nomi</label>
                    <input name="restaurant_name" value="{{ old('restaurant_name', $setting->restaurant_name) }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Logo</label>
                    <input name="logo" type="file" accept="image/*" class="w-full rounded-2xl border border-slate-200 px-3 py-2.5 dark:border-slate-700 dark:bg-slate-800">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Mavzu</label>
                    <select name="theme_preference" class="w-full rounded-2xl border border-slate-200 px-3 py-2.5 dark:border-slate-700 dark:bg-slate-800">
                        <option value="light" @selected(old('theme_preference', $setting->theme_preference) === 'light')>Yorug'</option>
                        <option value="dark" @selected(old('theme_preference', $setting->theme_preference) === 'dark')>Qorong'i</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <button type="submit" class="rounded-2xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Saqlash</button>
                </div>
            </form>
        </div>

        <div class="rounded-3xl bg-white p-6 shadow-soft dark:bg-slate-900">
            <h3 class="text-base font-semibold text-slate-900 dark:text-white">Preview</h3>
            <div class="mt-4 rounded-3xl bg-slate-50 p-5 dark:bg-slate-800/60">
                @if($setting->logo_path)
                    <img src="{{ asset('storage/'.$setting->logo_path) }}" class="h-16 w-16 rounded-2xl object-cover" alt="logo">
                @else
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-primary-100 p-3 dark:bg-primary-950/50">
                        <img src="{{ asset('Javohirlogo.png') }}" alt="logo" class="h-full w-full object-contain">
                    </div>
                @endif
                <p class="mt-4 text-lg font-semibold text-slate-900 dark:text-white">{{ $setting->restaurant_name }}</p>
                <p class="mt-1 text-sm text-slate-500">Sidebar va header shu nom bilan ishlaydi.</p>
            </div>
        </div>
    </div>
</x-app-layout>
