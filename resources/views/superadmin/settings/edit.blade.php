<x-layouts.superadmin :title="$pageTitle" :page-title="$pageTitle" page-subtitle="Brending, media assetlar va superadmin xavfsizlik sozlamalari.">
    <div class="grid gap-6 xl:grid-cols-[1.15fr_.85fr]">
        <x-superadmin.panel title="Platform settings" subtitle="Branding, kontakt va global media assetlar." icon="settings-2">
            <form method="POST" action="{{ route('superadmin.settings.update') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Platform nomi</label>
                        <input type="text" name="restaurant_name" value="{{ old('restaurant_name', $setting->restaurant_name) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Aloqa telefoni</label>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone', $setting->contact_phone) }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    @foreach($mediaCards as $card)
                        <div class="rounded-[24px] border border-slate-200 p-4">
                            <p class="font-semibold text-slate-900">{{ $card['label'] }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $card['description'] }}</p>
                            <input type="file" name="{{ $card['key'] }}" accept="{{ $card['accept'] }}" class="mt-4 block w-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-5 text-sm text-slate-500 file:mr-4 file:rounded-xl file:border-0 file:bg-slate-950 file:px-4 file:py-2 file:text-white">
                        </div>
                    @endforeach
                </div>

                <button class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Sozlamalarni saqlash</button>
            </form>
        </x-superadmin.panel>

        <div class="space-y-6">
            <x-superadmin.panel title="Superadmin security" subtitle="Superadmin kirish ma'lumotlarini admin paneldagi xavfsizlik oqimiga o'xshash tarzda yangilang." icon="shield-check">
                <form method="POST" action="{{ route('superadmin.settings.password.update') }}" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-loading-form x-data="{ showPasswords: false }">
                    @csrf
                    @method('PUT')

                    <div class="md:col-span-2 xl:col-span-3 flex flex-wrap justify-between gap-3">
                        <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-500">
                            Joriy foydalanuvchi:
                            <span class="font-semibold text-slate-900">{{ auth()->user()?->name ?? 'Superadmin' }}</span>
                        </div>
                        <button type="button" @click="showPasswords = !showPasswords" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">
                            <i data-lucide="eye" class="h-4 w-4" x-show="!showPasswords"></i>
                            <i data-lucide="eye-off" class="h-4 w-4" x-show="showPasswords" x-cloak></i>
                            <span x-text="showPasswords ? 'Parolni yashirish' : 'Parolni ko\\'rsatish'"></span>
                        </button>
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Joriy parol</label>
                        <div class="relative">
                            <input name="current_password" x-bind:type="showPasswords ? 'text' : 'password'" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 pr-12 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:bg-white">
                            <button type="button" @click="showPasswords = !showPasswords" class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 transition hover:text-slate-600" :aria-label="showPasswords ? 'Parolni yashirish' : 'Parolni ko\\'rsatish'">
                                <i data-lucide="eye" class="h-4 w-4" x-show="!showPasswords"></i>
                                <i data-lucide="eye-off" class="h-4 w-4" x-show="showPasswords" x-cloak></i>
                            </button>
                        </div>
                        @error('current_password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Yangi parol</label>
                        <div class="relative">
                            <input name="password" x-bind:type="showPasswords ? 'text' : 'password'" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 pr-12 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:bg-white">
                            <button type="button" @click="showPasswords = !showPasswords" class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 transition hover:text-slate-600" :aria-label="showPasswords ? 'Parolni yashirish' : 'Parolni ko\\'rsatish'">
                                <i data-lucide="eye" class="h-4 w-4" x-show="!showPasswords"></i>
                                <i data-lucide="eye-off" class="h-4 w-4" x-show="showPasswords" x-cloak></i>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-slate-400">Kamida 8 ta belgidan iborat, harf, raqam va maxsus belgi qatnashgan parol tavsiya etiladi.</p>
                        @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Parol tasdig'i</label>
                        <div class="relative">
                            <input name="password_confirmation" x-bind:type="showPasswords ? 'text' : 'password'" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 pr-12 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:bg-white">
                            <button type="button" @click="showPasswords = !showPasswords" class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 transition hover:text-slate-600" :aria-label="showPasswords ? 'Parolni yashirish' : 'Parolni ko\\'rsatish'">
                                <i data-lucide="eye" class="h-4 w-4" x-show="!showPasswords"></i>
                                <i data-lucide="eye-off" class="h-4 w-4" x-show="showPasswords" x-cloak></i>
                            </button>
                        </div>
                    </div>

                    <div class="md:col-span-2 xl:col-span-3 flex justify-end">
                        <button class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Parolni yangilash</button>
                    </div>
                </form>
            </x-superadmin.panel>

            <x-superadmin.panel title="Xavfsizlik tavsiyalari" subtitle="Superadmin akkauntini himoyalash uchun qisqa checklist." icon="lock-keyhole">
                <div class="space-y-3 text-sm text-slate-600">
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">Parol yangilansa xavfsizlik izlari audit log va security bo'limida aks etadi.</div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">Superadmin akkauntiga faqat ishonchli qurilmalardan kiring.</div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">Telegram token bu bo'limda emas, Integratsiyalar sahifasida alohida secure storage orqali boshqariladi.</div>
                </div>
            </x-superadmin.panel>
        </div>
    </div>
</x-layouts.superadmin>
