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

        <x-superadmin.panel title="Superadmin security" subtitle="Parolni yangilash va xavfsizlik tavsiyalari." icon="shield-check">
            <form method="POST" action="{{ route('superadmin.settings.password.update') }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Yangi parol</label>
                    <input type="password" name="password" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                </div>
                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Parol tasdig'i</label>
                    <input type="password" name="password_confirmation" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                </div>
                <button class="w-full rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Parolni yangilash</button>
            </form>

            <div class="mt-5 space-y-3 text-sm text-slate-600">
                <div class="rounded-2xl bg-slate-50 px-4 py-3">Logo va favicon global branding sifatida landing, auth va superadmin qatlamida ishlatiladi.</div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3">Parol yangilansa xavfsizlik izlari audit log va security bo'limida aks etadi.</div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3">Telegram token bu bo'limda emas, Integratsiyalar sahifasida alohida secure storage orqali boshqariladi.</div>
            </div>
        </x-superadmin.panel>
    </div>
</x-layouts.superadmin>
