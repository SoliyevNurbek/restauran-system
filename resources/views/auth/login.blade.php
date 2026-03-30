<x-layouts.guest title="Kirish | Restoran Boshqaruvi">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="w-full max-w-md rounded-3xl bg-white p-8 shadow-soft dark:bg-slate-900">
            <div class="mb-8 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-primary-100 text-primary-700">
                    <span class="text-xl font-bold">R</span>
                </div>
                <h1 class="mt-4 text-2xl font-semibold text-slate-900 dark:text-white">Restoran Admin Paneli</h1>
                <p class="mt-1 text-sm text-slate-500">Tizimga kirib jarayonlarni boshqaring</p>
            </div>

            <form method="POST" action="{{ route('login.store') }}" class="space-y-4" data-loading-form>
                @csrf
                <div>
                    <label for="username" class="mb-1 block text-sm font-medium">Kirish</label>
                    <input id="username" name="username" type="text" value="{{ old('username') }}" required
                           class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none ring-primary-500 transition focus:ring-2 dark:border-slate-700 dark:bg-slate-800">
                    @error('username')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password" class="mb-1 block text-sm font-medium">Parol</label>
                    <input id="password" name="password" type="password" required
                           class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none ring-primary-500 transition focus:ring-2 dark:border-slate-700 dark:bg-slate-800">
                    @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="w-full rounded-xl bg-primary-600 py-3 text-sm font-semibold text-white shadow-soft transition hover:bg-primary-700">
                    Kirish
                </button>
            </form>
        </div>
    </div>
</x-layouts.guest>
