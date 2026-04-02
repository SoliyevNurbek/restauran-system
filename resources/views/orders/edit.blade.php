<x-app-layout title="Bronni tahrirlash" pageTitle="Bronni tahrirlash">
    <div class="rounded-2xl bg-white p-4 shadow-soft dark:bg-slate-900 sm:p-6 xl:p-7">
        <form action="{{ route('bookings.update', $booking) }}" method="POST" data-loading-form>
            @csrf @method('PUT')
            @include('orders.form', ['booking' => $booking])
            <div class="mt-6 flex flex-col gap-3 border-t border-slate-200/70 pt-4 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500 dark:text-slate-400">O'zgarishlarni saqlashdan oldin vaqt, valyuta va to'lov turini tekshiring.</p>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Bronni yangilash</button>
            </div>
        </form>
    </div>
</x-app-layout>

