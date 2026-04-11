<div class="grid gap-6 xl:grid-cols-[minmax(0,1.15fr)_minmax(300px,0.85fr)]">
    <div class="space-y-6">
        <x-admin.section-card icon="shield-check" title="Profil va xavfsizlik" subtitle="Admin kirish ma'lumotlarini yangilang.">
            <form method="POST" action="{{ route('settings.password.update', ['section' => 'security']) }}" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-loading-form x-data="{ showPasswords: false }">
                @csrf
                @method('PUT')

                <div class="md:col-span-2 xl:col-span-3 flex justify-between gap-3">
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-500 dark:bg-slate-800/60 dark:text-slate-300">Joriy foydalanuvchi: <span class="font-semibold text-slate-900 dark:text-white">{{ $adminUser?->name ?? 'Admin' }}</span></div>
                    <button type="button" @click="showPasswords = !showPasswords" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                        <i data-lucide="eye" class="h-4 w-4" x-show="!showPasswords"></i>
                        <i data-lucide="eye-off" class="h-4 w-4" x-show="showPasswords" x-cloak></i>
                        <span x-text="showPasswords ? 'Parolni yashirish' : 'Parolni ko\\'rsatish'"></span>
                    </button>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Joriy parol</label>
                    <input name="current_password" x-bind:type="showPasswords ? 'text' : 'password'" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-primary-400 focus:bg-white focus:ring-4 focus:ring-primary-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-primary-500 dark:focus:bg-slate-900 dark:focus:ring-primary-950">
                    @error('current_password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Yangi parol</label>
                    <input name="password" x-bind:type="showPasswords ? 'text' : 'password'" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-primary-400 focus:bg-white focus:ring-4 focus:ring-primary-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-primary-500 dark:focus:bg-slate-900 dark:focus:ring-primary-950">
                    <p class="mt-1 text-xs text-slate-400">Kamida 8 ta belgidan iborat kuchli parol tavsiya etiladi.</p>
                    @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Parolni tasdiqlash</label>
                    <input name="password_confirmation" x-bind:type="showPasswords ? 'text' : 'password'" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-primary-400 focus:bg-white focus:ring-4 focus:ring-primary-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-primary-500 dark:focus:bg-slate-900 dark:focus:ring-primary-950">
                </div>
                <div class="md:col-span-2 xl:col-span-3 flex justify-end">
                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">Parolni yangilash</button>
                </div>
            </form>
        </x-admin.section-card>
    </div>

    <aside class="space-y-6">
        <x-admin.section-card icon="user-circle-2" title="Profil overview" subtitle="Joriy akkaunt bo'yicha xavfsizlik konteksti.">
            <div class="space-y-3">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-800/60">
                    <p class="text-xs text-slate-400">Foydalanuvchi</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $adminUser?->name ?? 'Admin' }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-800/60">
                    <p class="text-xs text-slate-400">Login</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $adminUser?->username ?? 'guest' }}</p>
                </div>
            </div>
        </x-admin.section-card>

        <x-admin.section-card icon="lock-keyhole" title="Xavfsizlik tavsiyalari" subtitle="Tenant admin akkauntini himoyalash uchun qisqa checklist.">
            <div class="space-y-3 text-sm text-slate-600 dark:text-slate-300">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-800/60">Kuchli parolni muntazam yangilab boring.</div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-800/60">Panelga faqat ishonchli qurilmalardan kiring.</div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-800/60">Umumiy qurilmada ishlasangiz sessiyani tugatishni unutmang.</div>
            </div>
        </x-admin.section-card>
    </aside>
</div>
