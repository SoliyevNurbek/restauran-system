<x-app-layout title="Oshxona xarajati" pageTitle="Oshxona xarajati qo'shish">
    <div class="rounded-2xl bg-white p-4 shadow-soft dark:bg-slate-900 sm:p-6 xl:p-7">
        <form action="{{ route('expenses.kitchen.store') }}" method="POST" class="grid gap-4 md:grid-cols-2" data-loading-form>
            @csrf
            @include('expenses.kitchen.form', ['cost' => null])
            <div class="md:col-span-2 flex flex-col gap-3 border-t border-slate-200/70 pt-4 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500 dark:text-slate-400">Bo'sh qo'shimcha xarajat maydonlari avtomatik 0 deb olinadi.</p>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Saqlash</button>
            </div>
        </form>
    </div>
</x-app-layout>
