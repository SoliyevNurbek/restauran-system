<x-app-layout title="Mijoz yaratish" pageTitle="Mijoz qo'shish">
    <div class="rounded-2xl bg-white p-6 shadow-soft dark:bg-slate-900">
        <form action="{{ route('customers.store') }}" method="POST" class="space-y-4" data-loading-form>
            @csrf
            @include('customers.form', ['customer' => null])
            <button type="submit" class="rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Mijozni saqlash</button>
        </form>
    </div>
</x-app-layout>
