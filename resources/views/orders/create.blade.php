<x-app-layout title="Bron yaratish" pageTitle="Bron yaratish">
    <div class="rounded-2xl bg-white p-6 shadow-soft dark:bg-slate-900">
        <form action="{{ route('bookings.store') }}" method="POST" data-loading-form>
            @csrf
            @include('orders.form', ['booking' => $booking])
            <div class="mt-5"><button type="submit" class="rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Bronni saqlash</button></div>
        </form>
    </div>
</x-app-layout>

