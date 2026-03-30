<x-app-layout title="Sozlamalar" pageTitle="Sozlamalar">
    <div class="rounded-2xl bg-white p-6 shadow-soft dark:bg-slate-900">
        <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="grid gap-4 md:grid-cols-2" data-loading-form>
            @csrf
            @method('PUT')

            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-medium">Restoran nomi</label>
                <input name="restaurant_name" value="{{ old('restaurant_name', $setting->restaurant_name) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
                @error('restaurant_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium">Logo</label>
                <input name="logo" type="file" accept="image/*" class="w-full rounded-xl border border-slate-200 px-3 py-2 dark:border-slate-700 dark:bg-slate-800">
                @error('logo')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium">Mavzu holati</label>
                <select name="theme_preference" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 dark:border-slate-700 dark:bg-slate-800">
                    <option value="light" @selected(old('theme_preference', $setting->theme_preference) === 'light')>Yorug'</option>
                    <option value="dark" @selected(old('theme_preference', $setting->theme_preference) === 'dark')>Qorong'i</option>
                </select>
            </div>

            @if($setting->logo_path)
                <div class="md:col-span-2">
                    <p class="mb-2 text-sm font-medium">Joriy logo</p>
                    <img src="{{ asset('storage/'.$setting->logo_path) }}" class="h-16 w-16 rounded-xl object-cover" alt="logo">
                </div>
            @endif

            <div class="md:col-span-2">
                <button type="submit" class="rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Sozlamalarni saqlash</button>
            </div>
        </form>
    </div>
</x-app-layout>
