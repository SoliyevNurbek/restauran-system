<x-app-layout title="Buyurtma yaratish" pageTitle="Buyurtma yaratish">
    <div class="rounded-2xl bg-white p-6 shadow-soft dark:bg-slate-900">
        <form action="{{ route('orders.store') }}" method="POST" data-loading-form>
            @csrf
            @include('orders.form', ['order' => null])
            <div class="mt-5"><button type="submit" class="rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Buyurtmani saqlash</button></div>
        </form>
    </div>
</x-app-layout>
