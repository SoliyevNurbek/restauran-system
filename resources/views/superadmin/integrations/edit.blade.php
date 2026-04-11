<x-layouts.superadmin :title="$pageTitle" :page-title="$pageTitle" :page-subtitle="$pageSubtitle">
    <div class="grid gap-6 xl:grid-cols-[1.1fr_.9fr]">
        <x-superadmin.panel title="Telegram bot integration" subtitle="Secure token storage, recipient va alert routing." icon="send">
            <form method="POST" action="{{ route('superadmin.integrations.telegram.update') }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Bot token</label>
                        <input type="password" name="bot_token" placeholder="{{ $telegram['configured'] ? 'Saved securely. Yangi token kiritilsa almashtiriladi.' : 'Telegram Bot Token' }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Chat ID yoki recipient</label>
                        <input type="text" name="chat_id" value="{{ $telegram['chat_id'] }}" placeholder="Masalan: -1001234567890" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    </div>
                </div>

                <div>
                    <label class="mb-3 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Alert types</label>
                    <div class="grid gap-3 md:grid-cols-2">
                        @foreach($telegram['alert_types'] as $alert)
                            <label class="flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700">
                                <input type="checkbox" name="alerts[]" value="{{ $alert }}" @checked(in_array($alert, $telegram['alerts'], true)) class="rounded border-slate-300">
                                <span>{{ str($alert)->replace('_', ' ')->headline() }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <button class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Integratsiyani saqlash</button>
            </form>
            <form method="POST" action="{{ route('superadmin.integrations.telegram.test') }}" class="mt-3">
                @csrf
                <button class="rounded-2xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700">Test connection</button>
            </form>
        </x-superadmin.panel>

        <x-superadmin.panel title="Operational guidance" subtitle="Telegram alert sifati va xavfsizlik talablari." icon="shield-check">
            <div class="space-y-3 text-sm leading-6 text-slate-600">
                <div class="rounded-2xl bg-slate-50 px-4 py-3">Token frontendda ko'rsatilmaydi. Yangi token faqat qayta yozish uchun qabul qilinadi.</div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3">Chat ID orqali bitta operator, guruh yoki private channel'ga alert uzatish mumkin.</div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3">Tasdiq, payment failure, suspicious login va settings change kabi signal turlari qo'llab-quvvatlanadi.</div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3">Telegram xabarlari strukturalangan premium formatda yuboriladi: heading, subject, status, qiymatlar va timestamp bilan.</div>
            </div>
        </x-superadmin.panel>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <x-superadmin.panel title="Click gateway" subtitle="Merchant identifikatorlari va checkout endpoint." icon="mouse-pointer-click">
            <form method="POST" action="{{ route('superadmin.integrations.billing.update') }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid gap-4">
                    <input type="text" name="click_service_id" value="{{ $billing['click']['service_id'] }}" placeholder="Service ID" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    <input type="text" name="click_merchant_id" value="{{ $billing['click']['merchant_id'] }}" placeholder="Merchant ID" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    <input type="password" name="click_secret_key" placeholder="{{ $billing['click']['secret_key'] ? 'Saved securely. Yangi qiymat kiritilsa almashtiriladi.' : 'Secret key' }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    <input type="text" name="click_merchant_user_id" value="{{ $billing['click']['merchant_user_id'] }}" placeholder="Merchant user ID" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    <input type="url" name="click_checkout_url" value="{{ $billing['click']['checkout_url'] }}" placeholder="Checkout URL" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                </div>

                <div class="grid gap-4 border-t border-slate-200 pt-4">
                    <input type="text" name="payme_merchant_id" value="{{ $billing['payme']['merchant_id'] }}" placeholder="Payme merchant ID" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    <input type="password" name="payme_secret_key" placeholder="{{ $billing['payme']['secret_key'] ? 'Saved securely. Yangi qiymat kiritilsa almashtiriladi.' : 'Payme secret key' }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    <input type="url" name="payme_checkout_url" value="{{ $billing['payme']['checkout_url'] }}" placeholder="Payme checkout URL" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    <input type="text" name="payme_account_key" value="{{ $billing['payme']['account_key'] }}" placeholder="Payme account key" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                </div>

                <button class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Billing gatewaylarni saqlash</button>
            </form>
        </x-superadmin.panel>

        <x-superadmin.panel title="Gateway notes" subtitle="Click va Payme ishlashi uchun zarur amaliy ko'rsatmalar." icon="credit-card">
            <div class="space-y-3 text-sm leading-6 text-slate-600">
                <div class="rounded-2xl bg-slate-50 px-4 py-3">Click uchun `prepare` va `complete` callback route'lari tenant billing moduliga ulangan.</div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3">Payme uchun Merchant API endpoint JSON-RPC formatda ishlaydi va subscription payment yozuvlarini idempotent yangilaydi.</div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3">Secret qiymatlar secure storage orqali shifrlanib saqlanadi.</div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3">Productionda provider kabinetlarida callback URL sifatida loyiha domenidagi billing route'larini ko'rsatish kerak.</div>
            </div>
        </x-superadmin.panel>
    </div>
</x-layouts.superadmin>
