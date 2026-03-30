<x-app-layout title="To'lov qo'shish" pageTitle="To'lov qo'shish">
    <div class="rounded-2xl bg-white p-6 shadow-soft dark:bg-slate-900">
        <form action="{{ route('payments.store') }}" method="POST" class="grid gap-4 md:grid-cols-2" data-loading-form>
            @csrf
            @include('payments.form', ['payment' => null])
            <div class="md:col-span-2"><button type="submit" class="rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Saqlash</button></div>
        </form>
    </div>
</x-app-layout>

