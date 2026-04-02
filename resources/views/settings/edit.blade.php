<x-app-layout title="Sozlamalar" pageTitle="Sozlamalar">
    <div class="space-y-6">
        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.35fr)_minmax(280px,0.65fr)]">
            <div class="space-y-6">
                <section class="rounded-[2rem] border border-slate-200/70 bg-white p-5 shadow-soft dark:border-slate-800 dark:bg-slate-900 sm:p-6">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Asosiy ma'lumotlar</h3>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="mt-6 grid gap-5 md:grid-cols-2" data-loading-form>
                        @csrf
                        @method('PUT')

                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Tizim nomi</label>
                            <input
                                name="restaurant_name"
                                value="{{ old('restaurant_name', $setting->restaurant_name) }}"
                                required
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-primary-400 focus:bg-white focus:ring-4 focus:ring-primary-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-primary-500 dark:focus:bg-slate-900 dark:focus:ring-primary-950"
                            >
                            @error('restaurant_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Telefon raqami</label>
                            <input
                                name="contact_phone"
                                type="text"
                                inputmode="tel"
                                placeholder="+998 90 123 45 67"
                                value="{{ old('contact_phone', $setting->contact_phone) }}"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-primary-400 focus:bg-white focus:ring-4 focus:ring-primary-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-primary-500 dark:focus:bg-slate-900 dark:focus:ring-primary-950"
                            >
                            <p class="mt-1 text-xs text-slate-400">Bo'sh qoldirilsa raqam o'chiriladi.</p>
                            @error('contact_phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Notification email</label>
                            <input
                                name="notification_email"
                                type="email"
                                placeholder="notify@restoran.uz"
                                value="{{ old('notification_email', $setting->notification_email) }}"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-primary-400 focus:bg-white focus:ring-4 focus:ring-primary-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-primary-500 dark:focus:bg-slate-900 dark:focus:ring-primary-950"
                            >
                            <p class="mt-1 text-xs text-slate-400">Xabarnomalar yuboriladigan email manzil.</p>
                            @error('notification_email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Logo</label>
                            <input id="logoInput" name="logo" type="file" accept="image/*" class="hidden">
                            <label for="logoInput" class="group block cursor-pointer overflow-hidden rounded-[28px] border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-primary-300 hover:shadow-md dark:border-slate-700 dark:from-slate-900 dark:to-slate-950">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-[24px] border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
                                        @if($setting->logo_path)
                                            <img id="logoPreview" src="{{ asset('storage/'.$setting->logo_path) }}" alt="Logo preview" class="h-full w-full object-cover">
                                        @else
                                            <img id="logoPreview" src="{{ asset('Javohirlogo.png') }}" alt="Logo preview" class="h-full w-full object-contain p-3">
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-medium text-primary-700 shadow-sm dark:bg-slate-800 dark:text-primary-300">Logo</div>
                                        <p class="mt-3 text-base font-semibold text-slate-900 transition group-hover:text-primary-700 dark:text-white dark:group-hover:text-primary-300">Rasm tanlash</p>
                                        <p class="mt-1 text-sm text-slate-400">PNG, JPG, WEBP</p>
                                    </div>
                                </div>
                            </label>
                            @error('logo')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Favicon</label>
                            <input id="faviconInput" name="favicon" type="file" accept="image/*" class="hidden">
                            <label for="faviconInput" class="group block cursor-pointer overflow-hidden rounded-[28px] border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-primary-300 hover:shadow-md dark:border-slate-700 dark:from-slate-900 dark:to-slate-950">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-[22px] border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
                                        @if($setting->favicon_path)
                                            <img id="faviconPreview" src="{{ asset('storage/'.$setting->favicon_path) }}" alt="Favicon preview" class="h-full w-full object-cover">
                                        @elseif($setting->logo_path)
                                            <img id="faviconPreview" src="{{ asset('storage/'.$setting->logo_path) }}" alt="Favicon preview" class="h-full w-full object-cover">
                                        @else
                                            <img id="faviconPreview" src="{{ asset('Javohirlogo.png') }}" alt="Favicon preview" class="h-full w-full object-contain p-3">
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-medium text-primary-700 shadow-sm dark:bg-slate-800 dark:text-primary-300">Favicon</div>
                                        <p class="mt-3 text-base font-semibold text-slate-900 transition group-hover:text-primary-700 dark:text-white dark:group-hover:text-primary-300">Ikonka tanlash</p>
                                        <p class="mt-1 text-sm text-slate-400">ICO, PNG, WEBP</p>
                                    </div>
                                </div>
                            </label>
                            @error('favicon')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="md:col-span-2 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-sm text-slate-500 dark:text-slate-400">Saqlangandan keyin preview bloklari ham yangi ma'lumotlar bilan yangilanadi.</p>
                            <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-primary-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-primary-700">
                                O'zgarishlarni saqlash
                            </button>
                        </div>
                    </form>
                </section>

                <section class="rounded-[2rem] border border-slate-200/70 bg-white p-5 shadow-soft dark:border-slate-800 dark:bg-slate-900 sm:p-6">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Admin parolini o'zgartirish</h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Qisqa va xavfsiz forma. Joriy parol tekshiriladi, keyin yangi parol saqlanadi.</p>
                        </div>
                        <div class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                            Minimal 8 ta belgi
                        </div>
                    </div>

                    <form method="POST" action="{{ route('settings.password.update') }}" class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-loading-form x-data="{ showPasswords: false }">
                        @csrf
                        @method('PUT')

                        <div class="md:col-span-2 xl:col-span-3 flex justify-end">
                            <button
                                type="button"
                                @click="showPasswords = !showPasswords"
                                class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                            >
                                <i data-lucide="eye" class="h-4 w-4" x-show="!showPasswords"></i>
                                <i data-lucide="eye-off" class="h-4 w-4" x-show="showPasswords" x-cloak></i>
                                <span x-text="showPasswords ? 'Parolni yashirish' : 'Parolni ko\\'rsatish'"></span>
                            </button>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Joriy parol</label>
                            <input
                                name="current_password"
                                x-bind:type="showPasswords ? 'text' : 'password'"
                                required
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-primary-400 focus:bg-white focus:ring-4 focus:ring-primary-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-primary-500 dark:focus:bg-slate-900 dark:focus:ring-primary-950"
                            >
                            @error('current_password')<p class="mt-1 text-xs text-red-600">↳ {{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Yangi parol</label>
                            <input
                                name="password"
                                x-bind:type="showPasswords ? 'text' : 'password'"
                                required
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-primary-400 focus:bg-white focus:ring-4 focus:ring-primary-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-primary-500 dark:focus:bg-slate-900 dark:focus:ring-primary-950"
                            >
                            @error('password')<p class="mt-1 text-xs text-red-600">↳ {{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Tasdiqlash</label>
                            <input
                                name="password_confirmation"
                                x-bind:type="showPasswords ? 'text' : 'password'"
                                required
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-primary-400 focus:bg-white focus:ring-4 focus:ring-primary-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-primary-500 dark:focus:bg-slate-900 dark:focus:ring-primary-950"
                            >
                            @error('password_confirmation')<p class="mt-1 text-xs text-red-600">↳ {{ $message }}</p>@enderror
                        </div>

                        <div class="md:col-span-2 xl:col-span-3 flex justify-end">
                            <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                                Parolni yangilash
                            </button>
                        </div>
                    </form>
                </section>
            </div>

            <aside class="space-y-6">
                <section class="rounded-[2rem] border border-slate-200/70 bg-white p-5 shadow-soft dark:border-slate-800 dark:bg-slate-900 sm:p-6">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Preview</h3>
                    <div class="mt-5 overflow-hidden rounded-[28px] border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-5 dark:border-slate-700 dark:from-slate-900 dark:to-slate-950">
                        @if($setting->logo_path)
                            <img src="{{ asset('storage/'.$setting->logo_path) }}" class="h-16 w-16 rounded-2xl object-cover" alt="logo">
                        @else
                            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-primary-100 p-3 dark:bg-primary-950/50">
                                <img src="{{ asset('Javohirlogo.png') }}" alt="logo" class="h-full w-full object-contain">
                            </div>
                        @endif

                        <div class="mt-5 rounded-[24px] border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                            <div class="flex items-center gap-3">
                                <div class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-900">
                                    @if($setting->favicon_path)
                                        <img src="{{ asset('storage/'.$setting->favicon_path) }}" alt="favicon" class="h-full w-full object-cover">
                                    @elseif($setting->logo_path)
                                        <img src="{{ asset('storage/'.$setting->logo_path) }}" alt="favicon" class="h-full w-full object-cover">
                                    @else
                                        <img src="{{ asset('Javohirlogo.png') }}" alt="favicon" class="h-full w-full object-contain p-2">
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-lg font-semibold text-slate-900 dark:text-white">{{ $setting->restaurant_name }}</p>
                                    <p class="mt-1 text-sm text-slate-400">Tizim preview</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 space-y-3">
                            <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-900">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Telefon</p>
                                <p class="mt-2 text-sm font-medium text-slate-700 dark:text-slate-200">{{ $setting->contact_phone ?: 'Kiritilmagan' }}</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-900">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Notification email</p>
                                <p class="mt-2 text-sm font-medium text-slate-700 dark:text-slate-200 break-all">{{ $setting->notification_email ?: 'Ulanmagan' }}</p>
                            </div>
                        </div>
                    </div>
                </section>

            </aside>
        </div>
    </div>

    <script>
        (() => {
            const bindPreview = (inputId, previewId) => {
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);

                if (!input || !preview) return;

                input.addEventListener('change', () => {
                    const file = input.files?.[0];
                    if (!file) return;

                    const reader = new FileReader();
                    reader.onload = (event) => {
                        preview.src = event.target?.result;
                        preview.classList.remove('p-3');
                        preview.classList.add('object-cover');
                    };
                    reader.readAsDataURL(file);
                });
            };

            bindPreview('logoInput', 'logoPreview');
            bindPreview('faviconInput', 'faviconPreview');
        })();
    </script>
</x-app-layout>
