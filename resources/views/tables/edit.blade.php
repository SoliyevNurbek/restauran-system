<x-app-layout title="Zalni tahrirlash" pageTitle="Zalni tahrirlash">
    <div class="rounded-2xl bg-white p-6 shadow-soft dark:bg-slate-900">
        <form action="{{ route('halls.update', $hall) }}" method="POST" enctype="multipart/form-data" class="grid gap-4 md:grid-cols-2" data-loading-form>
            @csrf @method('PUT')
            @include('tables.form', ['hall' => $hall])
            <div class="md:col-span-2">
                <button type="submit" class="rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Zalni yangilash</button>
            </div>
        </form>
    </div>
</x-app-layout>

