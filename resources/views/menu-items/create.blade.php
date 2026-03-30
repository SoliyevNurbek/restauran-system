<x-app-layout title="Taom yaratish" pageTitle="Taom qo'shish Taom">
    <div class="rounded-2xl bg-white p-6 shadow-soft dark:bg-slate-900">
        <form action="{{ route('menu-items.store') }}" method="POST" enctype="multipart/form-data" class="grid gap-4 md:grid-cols-2" data-loading-form>
            @csrf
            @include('menu-items.form', ['menuTaom' => null])
            <div class="md:col-span-2">
                <button type="submit" class="rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Taomni saqlash</button>
            </div>
        </form>
    </div>
</x-app-layout>
