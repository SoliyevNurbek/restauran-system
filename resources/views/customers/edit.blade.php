<x-app-layout title="Mijozni tahrirlash" pageTitle="Mijozni tahrirlash">
    <div class="rounded-2xl bg-white p-6 shadow-soft dark:bg-slate-900">
        <form action="{{ route('customers.update', $customer) }}" method="POST" class="space-y-4" data-loading-form>
            @csrf @method('PUT')
            @include('customers.form', ['customer' => $customer])
            <button type="submit" class="rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Mijozni yangilash</button>
        </form>
    </div>
</x-app-layout>
