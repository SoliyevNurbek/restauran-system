<div class="grid gap-6 xl:grid-cols-[minmax(0,1.35fr)_minmax(300px,0.65fr)]">
    <div class="space-y-6">
        <x-admin.section-card icon="building-2" title="Biznes ma'lumotlari" subtitle="Nom, aloqa va brend ko'rinishini yangilang.">
            <form method="POST" action="{{ route('settings.update', ['section' => 'business']) }}" enctype="multipart/form-data" class="grid gap-5 md:grid-cols-2" data-loading-form>
                @csrf
                @method('PUT')

                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Toyxona nomi</label>
                    <input name="restaurant_name" value="{{ old('restaurant_name', $setting->restaurant_name) }}" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-primary-400 focus:bg-white focus:ring-4 focus:ring-primary-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-primary-500 dark:focus:bg-slate-900 dark:focus:ring-primary-950">
                    @error('restaurant_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Telefon raqami</label>
                    <input name="contact_phone" type="text" inputmode="tel" placeholder="+998 90 123 45 67" value="{{ old('contact_phone', $setting->contact_phone) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-primary-400 focus:bg-white focus:ring-4 focus:ring-primary-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-primary-500 dark:focus:bg-slate-900 dark:focus:ring-primary-950">
                    <p class="mt-1 text-xs text-slate-400">Mijozlar bilan aloqada ishlatiladi.</p>
                    @error('contact_phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="rounded-[28px] border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/60">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Mas'ul foydalanuvchi</p>
                    <div class="mt-3 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-2xl bg-white px-4 py-3 shadow-sm dark:bg-slate-900">
                            <p class="text-xs text-slate-400">Ism</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $adminUser?->name ?? 'Belgilanmagan' }}</p>
                        </div>
                        <div class="rounded-2xl bg-white px-4 py-3 shadow-sm dark:bg-slate-900">
                            <p class="text-xs text-slate-400">Login</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $adminUser?->username ?? 'guest' }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Logo</label>
                    <input id="logoInput" name="logo" type="file" accept="image/*" class="hidden">
                    <label for="logoInput" class="group block cursor-pointer overflow-hidden rounded-[28px] border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-primary-300 hover:shadow-md dark:border-slate-700 dark:from-slate-900 dark:to-slate-950">
                        <div class="flex items-center gap-4">
                            <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-[24px] border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
                                @if($mediaAssets->get('brand_logo')?->url())
                                    <img id="logoPreview" src="{{ $mediaAssets->get('brand_logo')->url() }}" alt="Logo preview" class="h-full w-full object-cover">
                                @elseif($setting->logoUrl())
                                    <img id="logoPreview" src="{{ $setting->logoUrl() }}" alt="Logo preview" class="h-full w-full object-cover">
                                @else
                                    <div id="logoPreview" class="flex h-full w-full items-center justify-center text-sm font-bold text-primary-700">MR</div>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-medium text-primary-700 shadow-sm dark:bg-slate-800 dark:text-primary-300">Brend</div>
                                <p class="mt-3 text-base font-semibold text-slate-900 transition group-hover:text-primary-700 dark:text-white dark:group-hover:text-primary-300">Logo yuklash</p>
                                <p class="mt-1 text-sm text-slate-400">PNG, JPG yoki WEBP</p>
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
                                @if($mediaAssets->get('brand_favicon')?->url())
                                    <img id="faviconPreview" src="{{ $mediaAssets->get('brand_favicon')->url() }}" alt="Favicon preview" class="h-full w-full object-cover">
                                @elseif($mediaAssets->get('brand_logo')?->url())
                                    <img id="faviconPreview" src="{{ $mediaAssets->get('brand_logo')->url() }}" alt="Favicon preview" class="h-full w-full object-cover">
                                @elseif($setting->faviconUrl())
                                    <img id="faviconPreview" src="{{ $setting->faviconUrl() }}" alt="Favicon preview" class="h-full w-full object-cover">
                                @elseif($setting->logoUrl())
                                    <img id="faviconPreview" src="{{ $setting->logoUrl() }}" alt="Favicon preview" class="h-full w-full object-cover">
                                @else
                                    <div id="faviconPreview" class="flex h-full w-full items-center justify-center text-sm font-bold text-primary-700">MR</div>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-medium text-primary-700 shadow-sm dark:bg-slate-800 dark:text-primary-300">Ikonka</div>
                                <p class="mt-3 text-base font-semibold text-slate-900 transition group-hover:text-primary-700 dark:text-white dark:group-hover:text-primary-300">Favicon yangilash</p>
                                <p class="mt-1 text-sm text-slate-400">ICO, PNG yoki WEBP</p>
                            </div>
                        </div>
                    </label>
                    @error('favicon')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-slate-500">Saqlangandan keyin tenant paneldagi brend ko'rinishi yangilanadi.</p>
                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-primary-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-primary-700">O'zgarishlarni saqlash</button>
                </div>
            </form>
        </x-admin.section-card>
    </div>

    <aside class="space-y-6">
        <x-admin.section-card icon="monitor-smartphone" title="Brend preview" subtitle="Tenant panelda ko'rinadigan joriy brend ko'rinishi.">
            <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-5 dark:border-slate-700 dark:from-slate-900 dark:to-slate-950">
                @if($mediaAssets->get('brand_logo')?->url())
                    <img src="{{ $mediaAssets->get('brand_logo')->url() }}" class="h-16 w-16 rounded-2xl object-cover" alt="logo">
                @elseif($setting->logoUrl())
                    <img src="{{ $setting->logoUrl() }}" class="h-16 w-16 rounded-2xl object-cover" alt="logo">
                @else
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-primary-100 p-3 text-sm font-bold text-primary-700 dark:bg-primary-950/50 dark:text-primary-300">MR</div>
                @endif
                <div class="mt-5 rounded-[24px] border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                    <p class="text-lg font-semibold text-slate-900 dark:text-white">{{ $setting->restaurant_name }}</p>
                    <p class="mt-1 text-sm text-slate-400">{{ $setting->contact_phone ?: 'Telefon kiritilmagan' }}</p>
                    <p class="mt-3 text-sm text-slate-500">{{ $adminUser?->name ?? 'Admin' }} | {{ $adminUser?->username ?? 'guest' }}</p>
                </div>
            </div>
        </x-admin.section-card>

        <x-admin.section-card icon="badge-check" title="SaaS holati" subtitle="Tenant tarif va billing ko'rinishi.">
            <div class="space-y-3">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-800/60">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Tarif</p>
                    <div class="mt-2 flex items-center gap-2">
                        <span class="text-base font-semibold text-slate-900 dark:text-white">{{ $subscription?->plan?->name ?? 'Basic' }}</span>
                        <span class="rounded-full bg-slate-200 px-2 py-1 text-[11px] font-semibold text-slate-700 dark:bg-slate-700 dark:text-slate-200">{{ ucfirst($subscription?->status ?? 'trial') }}</span>
                    </div>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-800/60">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Keyingi to'lov</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">{{ ($subscription?->renews_at ?? $subscription?->trial_ends_at ?? $subscription?->expires_at)?->format('d.m.Y') ?? 'Mavjud emas' }}</p>
                </div>
            </div>
        </x-admin.section-card>
    </aside>

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
                        let target = preview;

                        if (preview.tagName !== 'IMG') {
                            target = document.createElement('img');
                            target.id = preview.id;
                            target.alt = 'Preview';
                            target.className = 'h-full w-full object-cover';
                            preview.replaceWith(target);
                        }

                        target.src = event.target?.result;
                    };

                    reader.readAsDataURL(file);
                });
            };

            bindPreview('logoInput', 'logoPreview');
            bindPreview('faviconInput', 'faviconPreview');
        })();
    </script>
</div>
