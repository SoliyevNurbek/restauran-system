<x-layouts.superadmin :title="$pageTitle" :page-title="$pageTitle" :page-subtitle="$pageSubtitle">
    <div class="grid gap-6 xl:grid-cols-[1.1fr_.9fr]">
        <x-superadmin.panel title="Bot konfiguratsiyasi" subtitle="Manual to'lovlar va platforma alertlari uchun yagona Telegram bot." icon="send">
            <form method="POST" action="{{ route('superadmin.telegram.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid gap-4 md:grid-cols-2">
                    <label class="flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3 text-sm md:col-span-2">
                        <input type="checkbox" name="is_enabled" value="1" @checked($telegramWorkflow['is_enabled']) class="rounded border-slate-300">
                        <span>Telegram workflow faollashtirilgan</span>
                    </label>

                    <input type="password" name="bot_token" placeholder="{{ $telegramWorkflow['bot_token'] ? 'Saved securely. Yangi token kiritilsa almashtiriladi.' : 'Bot token' }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm md:col-span-2">
                    <input type="text" name="bot_username" value="{{ $telegramWorkflow['bot_username'] }}" placeholder="Bot username" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    <input type="password" name="webhook_secret" placeholder="{{ $telegramWorkflow['webhook_secret'] ? 'Saved securely. Yangi secret kiritilsa almashtiriladi.' : 'Webhook secret' }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    <input type="text" name="admin_chat_id" value="{{ $telegramWorkflow['admin_chat_id'] }}" placeholder="Admin chat ID" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    <input type="text" name="payment_card_number" value="{{ $telegramWorkflow['payment_card_number'] }}" placeholder="To'lov kartasi raqami" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    <input type="text" name="payment_card_holder" value="{{ $telegramWorkflow['payment_card_holder'] }}" placeholder="Karta egasi" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                </div>

                <div>
                    <p class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Notification toggles</p>
                    <div class="grid gap-3 md:grid-cols-2">
                        @foreach($notificationTypes as $type)
                            <label class="flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700">
                                <input type="checkbox" name="notification_settings[{{ $type }}]" value="1" @checked($telegramWorkflow['notification_settings'][$type] ?? false) class="rounded border-slate-300">
                                <span>{{ str($type)->replace('_', ' ')->headline() }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid gap-4">
                    <textarea name="message_templates[payment_instruction]" rows="4" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" placeholder="Payment instruction template">{{ $telegramWorkflow['message_templates']['payment_instruction'] ?? '' }}</textarea>
                    <textarea name="message_templates[payment_approved]" rows="3" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" placeholder="Approval template">{{ $telegramWorkflow['message_templates']['payment_approved'] ?? '' }}</textarea>
                    <textarea name="message_templates[payment_rejected]" rows="3" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" placeholder="Rejection template">{{ $telegramWorkflow['message_templates']['payment_rejected'] ?? '' }}</textarea>
                    <textarea name="message_templates[expiry_reminder]" rows="3" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" placeholder="Expiry reminder template">{{ $telegramWorkflow['message_templates']['expiry_reminder'] ?? '' }}</textarea>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Saqlash</button>
                </div>
            </form>

            <form method="POST" action="{{ route('superadmin.telegram.test') }}" class="mt-3">
                @csrf
                <button class="rounded-2xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700">Test xabar yuborish</button>
            </form>
        </x-superadmin.panel>

        <x-superadmin.panel title="Workflow ko'rsatma" subtitle="Tenant manual payment jarayoni qanday ishlashini nazorat qilish uchun." icon="shield-check">
            <div class="space-y-3 text-sm leading-6 text-slate-600">
                <div class="rounded-2xl bg-slate-50 px-4 py-3">Tenant tarif tanlaganda manual Telegram payment request yaratiladi va bot yo'riqnoma yuboradi.</div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3">Foydalanuvchi botga chek yuborgach yozuv `under_review` holatiga o'tadi va superadmin review panelida ko'rinadi.</div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3">Tasdiqlash obunani faollashtiradi; rad etish esa sabab bilan foydalanuvchiga Telegram xabar yuboradi.</div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3">Bot token frontendda ko'rsatilmaydi. Hamma maxfiy qiymatlar shifrlangan integration storage'da saqlanadi.</div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3">Webhook secret yoqilsa, Telegram webhook faqat maxsus header bilan qabul qilinadi.</div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3">Telegram webhook URL: <span class="font-semibold text-slate-900">{{ route('telegram.webhook') }}</span></div>
            </div>
        </x-superadmin.panel>
    </div>
</x-layouts.superadmin>
