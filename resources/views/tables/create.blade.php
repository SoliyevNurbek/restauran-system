<x-app-layout title="Stol yaratish" pageTitle="Stol qo'shish">
    <div class="rounded-2xl bg-white p-6 shadow-soft dark:bg-slate-900">
        <form action="{{ route('tables.store') }}" method="POST" class="space-y-4" data-loading-form>
            @csrf
            @include('tables.form', ['table' => null])
            <button type="submit" class="rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Stolni saqlash</button>
        </form>
    </div>
</x-app-layout>
