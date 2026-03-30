<x-app-layout title="Ta'minotchini tahrirlash" pageTitle="Ta'minotchini tahrirlash">
    <div class="rounded-3xl bg-white p-6 shadow-soft dark:bg-slate-900">
        <form method="POST" action="{{ route('suppliers.update', $supplier) }}" data-loading-form>
            @csrf
            @method('PUT')
            @include('suppliers.form', ['supplier' => $supplier])

            <div class="mt-6 flex gap-3">
                <button type="submit" class="rounded-2xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Yangilash</button>
                <a href="{{ route('suppliers.index') }}" class="rounded-2xl border border-slate-200 px-5 py-2.5 text-sm font-medium dark:border-slate-700">Orqaga</a>
            </div>
        </form>
    </div>
</x-app-layout>
