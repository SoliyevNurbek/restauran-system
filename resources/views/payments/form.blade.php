<div>
    <label class="mb-1 block text-sm font-medium">Bron</label>
    <select name="booking_id" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
        <option value="">Bronni tanlang</option>
        @foreach($bookings as $booking)
            <option value="{{ $booking->id }}" @selected(old('booking_id', $payment?->booking_id) == $booking->id)>{{ $booking->booking_number }} - {{ $booking->client?->full_name }}</option>
        @endforeach
    </select>
    @error('booking_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Miqdor</label>
    <input name="amount" type="number" step="0.01" min="0.01" value="{{ old('amount', $payment?->amount) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('amount')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">To'lov usuli</label>
    <select name="payment_method" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
        @foreach($methods as $method)
            <option value="{{ $method }}" @selected(old('payment_method', $payment?->payment_method ?? 'Naqd') === $method)>{{ $method }}</option>
        @endforeach
    </select>
    @error('payment_method')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="mb-1 block text-sm font-medium">Sana</label>
    <input name="payment_date" type="date" value="{{ old('payment_date', optional($payment?->payment_date)->format('Y-m-d') ?? now()->toDateString()) }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">
    @error('payment_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
<div class="md:col-span-2">
    <label class="mb-1 block text-sm font-medium">Izoh</label>
    <textarea name="note" rows="4" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 dark:border-slate-700 dark:bg-slate-800">{{ old('note', $payment?->note) }}</textarea>
    @error('note')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>

