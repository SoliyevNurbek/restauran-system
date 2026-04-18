<x-layouts.guest title="Superadmin login">
    <div class="flex min-h-screen items-center justify-center bg-slate-950 px-4">
        <form method="POST" action="{{ route('superadmin.login.store') }}" class="w-full max-w-md rounded-3xl border border-slate-800 bg-slate-900 p-8 shadow-2xl" x-data="{ showPassword: false }">
            @csrf
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-indigo-400">Superadmin</p>
            <h1 class="mt-3 text-3xl font-semibold text-white">Monitoring paneliga kirish</h1>
            <p class="mt-2 text-sm text-slate-400">Landing, approval va monitoring boshqaruvi.</p>
            <div class="mt-6 space-y-4">
                <div><label class="mb-2 block text-sm text-slate-300">Username</label><input name="username" value="{{ old('username') }}" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-white" required></div>
                <div>
                    <label class="mb-2 block text-sm text-slate-300">Parol</label>
                    <div class="relative">
                        <input name="password" x-bind:type="showPassword ? 'text' : 'password'" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 pr-12 text-white" required>
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 transition hover:text-white" :aria-label="showPassword ? 'Parolni yashirish' : 'Parolni ko\\'rsatish'">
                            <i data-lucide="eye" class="h-4 w-4" x-show="!showPassword"></i>
                            <i data-lucide="eye-off" class="h-4 w-4" x-show="showPassword" x-cloak></i>
                        </button>
                    </div>
                </div>
            </div>
            @error('username')<p class="mt-3 text-sm text-rose-400">{{ $message }}</p>@enderror
            <label class="mt-5 inline-flex items-center gap-2 text-sm text-slate-300">
                <input type="checkbox" name="remember" value="1" @checked(old('remember')) class="h-4 w-4 rounded border-slate-600 bg-slate-950 text-indigo-500 focus:ring-indigo-500">
                <span>Meni eslab qol</span>
            </label>
            <button type="submit" class="mt-6 w-full rounded-2xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white">Kirish</button>
        </form>
    </div>
</x-layouts.guest>
