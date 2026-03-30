<x-app-layout title="Bronni tahrirlash" pageTitle="Bronni tahrirlash">
    <div class="rounded-2xl bg-white p-6 shadow-soft dark:bg-slate-900">
        <form action="{{ route('bookings.update', $booking) }}" method="POST" data-loading-form>
            @csrf @method('PUT')
            @include('orders.form', ['booking' => $booking])
            <div class="mt-5"><button type="submit" class="rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Bronni yangilash</button></div>
        </form>
    </div>
</x-app-layout>

