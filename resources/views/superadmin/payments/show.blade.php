<x-layouts.superadmin :title="$pageTitle" :page-title="$pageTitle" :page-subtitle="$pageSubtitle">
    <div class="grid gap-6 xl:grid-cols-[1.1fr_.9fr]">
        <x-superadmin.panel title="Payment detail" subtitle="Invoice, proof va bog'liq subscription." icon="credit-card">
            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-2xl bg-slate-50 px-4 py-4"><p class="text-xs text-slate-500">Invoice</p><p class="mt-1 font-semibold text-slate-900">{{ $paymentRecord->invoice_number ?: 'No invoice' }}</p></div>
                <div class="rounded-2xl bg-slate-50 px-4 py-4"><p class="text-xs text-slate-500">Business</p><p class="mt-1 font-semibold text-slate-900">{{ $paymentRecord->venueConnection?->venue_name ?? 'N/A' }}</p></div>
                <div class="rounded-2xl bg-slate-50 px-4 py-4"><p class="text-xs text-slate-500">Plan</p><p class="mt-1 font-semibold text-slate-900">{{ $paymentRecord->plan?->name ?? $paymentRecord->subscription?->plan?->name ?? 'N/A' }}</p></div>
                <div class="rounded-2xl bg-slate-50 px-4 py-4"><p class="text-xs text-slate-500">Method</p><p class="mt-1 font-semibold text-slate-900">{{ $paymentRecord->paymentMethod?->label ?? 'N/A' }} / {{ str($paymentRecord->provider)->replace('_', ' ')->headline() }}</p></div>
                <div class="rounded-2xl bg-slate-50 px-4 py-4"><p class="text-xs text-slate-500">Amount</p><p class="mt-1 font-semibold text-slate-900">{{ number_format((float) $paymentRecord->amount, 0, '.', ' ') }} {{ $paymentRecord->currency }}</p></div>
                <div class="rounded-2xl bg-slate-50 px-4 py-4"><p class="text-xs text-slate-500">Status</p><div class="mt-2"><x-superadmin.status-badge :status="$paymentRecord->status" /></div></div>
                <div class="rounded-2xl bg-slate-50 px-4 py-4"><p class="text-xs text-slate-500">Telegram chat</p><p class="mt-1 font-semibold text-slate-900">{{ $paymentRecord->telegram_chat_id ?: 'Ulanmagan' }}</p></div>
                <div class="rounded-2xl bg-slate-50 px-4 py-4"><p class="text-xs text-slate-500">Reviewer</p><p class="mt-1 font-semibold text-slate-900">{{ $paymentRecord->reviewer?->name ?? "Ko'rilmagan" }}</p></div>
            </div>

            @if($paymentRecord->notes)
                <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-4 text-sm text-slate-600">{{ $paymentRecord->notes }}</div>
            @endif

            @if($paymentRecord->proof_note || $paymentRecord->rejection_reason || $paymentRecord->internal_note)
                <div class="mt-5 grid gap-4 md:grid-cols-3">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-4 text-sm text-slate-600">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Proof note</p>
                        <p class="mt-2">{{ $paymentRecord->proof_note ?: "Yo'q" }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-4 text-sm text-slate-600">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Internal note</p>
                        <p class="mt-2">{{ $paymentRecord->internal_note ?: "Yo'q" }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-4 text-sm text-slate-600">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Rejection reason</p>
                        <p class="mt-2">{{ $paymentRecord->rejection_reason ?: "Yo'q" }}</p>
                    </div>
                </div>
            @endif

            @if($paymentRecord->proof_file_path)
                <div class="mt-5">
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <h3 class="font-semibold text-slate-900">To'lov cheki preview</h3>
                        <a href="{{ route('superadmin.payments.proof', $paymentRecord) }}" target="_blank" rel="noopener noreferrer" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700">To'liq ko'rish</a>
                    </div>
                    <div class="overflow-hidden rounded-[24px] border border-slate-200 bg-white">
                        <img src="{{ route('superadmin.payments.proof', $paymentRecord) }}" alt="Payment proof" class="max-h-[460px] w-full object-contain">
                    </div>
                </div>
            @endif

            @if($paymentRecord->telegramMessages->isNotEmpty())
                <div class="mt-5 rounded-[24px] border border-slate-200 p-4">
                    <h3 class="font-semibold text-slate-900">Telegram log</h3>
                    <div class="mt-4 space-y-3">
                        @foreach($paymentRecord->telegramMessages->take(6) as $message)
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="font-semibold text-slate-900">{{ str($message->message_type)->headline() }}</span>
                                    <span class="text-xs text-slate-400">{{ $message->created_at?->format('d.m.Y H:i') }}</span>
                                </div>
                                @if($message->content)
                                    <p class="mt-2">{{ $message->content }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </x-superadmin.panel>

        <div class="space-y-6">
            <x-superadmin.panel title="Review payment" subtitle="Manual Telegram to'lovi bo'yicha approve yoki reject qarorini qabul qiling." icon="square-pen">
                @if($paymentRecord->provider === 'manual_telegram' && ! in_array($paymentRecord->status, ['paid', 'rejected'], true))
                    <form method="POST" action="{{ route('superadmin.payments.review', $paymentRecord) }}" class="space-y-4">
                        @csrf
                        <textarea name="internal_note" rows="3" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" placeholder="Ichki izoh">{{ old('internal_note', $paymentRecord->internal_note) }}</textarea>
                        <button name="action" value="approve" class="w-full rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white">Tasdiqlash va obunani faollashtirish</button>
                        <textarea name="rejection_reason" rows="3" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" placeholder="Rad etish sababi">{{ old('rejection_reason', $paymentRecord->rejection_reason) }}</textarea>
                        <button name="action" value="reject" class="w-full rounded-2xl bg-rose-600 px-4 py-3 text-sm font-semibold text-white">Rad etish va foydalanuvchiga xabar yuborish</button>
                    </form>
                @else
                    <div class="rounded-2xl bg-slate-50 px-4 py-4 text-sm text-slate-600">Bu yozuv bo'yicha review jarayoni yakunlangan yoki manual Telegram to'lovi emas.</div>
                @endif
            </x-superadmin.panel>

            <x-superadmin.panel title="Update payment" subtitle="Status, paid date va note oqimi." icon="square-pen">
                <form method="POST" action="{{ route('superadmin.payments.update', $paymentRecord) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid gap-4 sm:grid-cols-2">
                        <select name="status" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                            @foreach(['paid','pending','payment_details_sent','awaiting_proof','under_review','failed','rejected','refunded','canceled'] as $status)
                                <option value="{{ $status }}" @selected($paymentRecord->status === $status)>{{ str($status)->headline() }}</option>
                            @endforeach
                        </select>
                        <input type="number" name="amount" value="{{ $paymentRecord->amount }}" min="0" step="0.01" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        <input type="text" name="currency" value="{{ $paymentRecord->currency }}" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        <input type="date" name="due_date" value="{{ optional($paymentRecord->due_date)->format('Y-m-d') }}" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        <input type="date" name="paid_at" value="{{ optional($paymentRecord->paid_at)->format('Y-m-d') }}" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm sm:col-span-2">
                        <input type="text" name="invoice_number" value="{{ $paymentRecord->invoice_number }}" placeholder="Invoice" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm sm:col-span-2">
                        <input type="text" name="transaction_reference" value="{{ $paymentRecord->transaction_reference }}" placeholder="Reference" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm sm:col-span-2">
                    </div>
                    <textarea name="notes" rows="4" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" placeholder="Payment notes">{{ $paymentRecord->notes }}</textarea>
                    <textarea name="internal_note" rows="3" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" placeholder="Internal note">{{ $paymentRecord->internal_note }}</textarea>
                    <textarea name="rejection_reason" rows="3" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" placeholder="Rejection reason">{{ $paymentRecord->rejection_reason }}</textarea>
                    <button class="w-full rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white">To'lovni yangilash</button>
                </form>
            </x-superadmin.panel>
        </div>
    </div>
</x-layouts.superadmin>
