<x-app-layout title="Kategoriya yaratish" pageTitle="Kategoriya yaratish">
    <div class="rounded-2xl bg-white p-6 shadow-soft dark:bg-slate-900">
        <form action="{{ route('categories.store') }}" method="POST" class="space-y-4" data-loading-form>
            @csrf
            @include('categories.form', ['category' => null])
            <button type="submit" class="rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Kategoriyani saqlash</button>
        </form>
    </div>
</x-app-layout>
