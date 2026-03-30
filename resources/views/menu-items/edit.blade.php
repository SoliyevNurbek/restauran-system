<x-app-layout title="Taomni tahrirlash" pageTitle="Taomni tahrirlash Taom">
    <div class="rounded-2xl bg-white p-6 shadow-soft dark:bg-slate-900">
        <form action="{{ route('menu-items.update', $menuTaom) }}" method="POST" enctype="multipart/form-data" class="grid gap-4 md:grid-cols-2" data-loading-form>
            @csrf @method('PUT')
            @include('menu-items.form', ['menuTaom' => $menuTaom])
            <div class="md:col-span-2">
                <button type="submit" class="rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Taomni yangilash</button>
            </div>
        </form>
    </div>
</x-app-layout>
