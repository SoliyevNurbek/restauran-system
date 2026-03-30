<x-app-layout title="Paketni tahrirlash" pageTitle="To'y paketini tahrirlash">
    <div class="rounded-2xl bg-white p-6 shadow-soft dark:bg-slate-900">
        <form action="{{ route('wedding-packages.update', $weddingPackage) }}" method="POST" enctype="multipart/form-data" class="grid gap-4 md:grid-cols-2" data-loading-form>
            @csrf @method('PUT')
            @include('menu-items.form', ['weddingPackage' => $weddingPackage])
            <div class="md:col-span-2">
                <button type="submit" class="rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Paketni yangilash</button>
            </div>
        </form>
    </div>
</x-app-layout>

