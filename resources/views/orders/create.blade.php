<x-app-layout title="Bron yaratish" pageTitle="Bron yaratish">
    <div class="rounded-2xl bg-white p-4 shadow-soft dark:bg-slate-900 sm:p-6 xl:p-7">
        <form action="{{ route('bookings.store') }}" method="POST" data-loading-form>
            @csrf
            @include('orders.form', ['booking' => $booking])
            <div class="mt-6 flex flex-col gap-3 border-t border-slate-200/70 pt-4 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500 dark:text-slate-400">Ma'lumotlarni tekshirib, so'ng bronni saqlang.</p>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Bronni saqlash</button>
            </div>
        </form>
    </div>
</x-app-layout>

