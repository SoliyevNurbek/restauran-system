<x-app-layout title="Asosiy mahsulot qo'shish" pageTitle="Asosiy mahsulot qo'shish">
    <div class="rounded-3xl bg-white p-6 shadow-soft dark:bg-slate-900">
        <form method="POST" action="{{ route('products.store') }}" data-loading-form>
            @csrf
            @include('master-products.form')

            <div class="mt-6 flex gap-3">
                <button type="submit" class="rounded-2xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Saqlash</button>
                <a href="{{ route('products.index') }}" class="rounded-2xl border border-slate-200 px-5 py-2.5 text-sm font-medium dark:border-slate-700">Bekor qilish</a>
            </div>
        </form>
    </div>
</x-app-layout>
