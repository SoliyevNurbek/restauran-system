<x-app-layout title="Tadbir turini tahrirlash" pageTitle="Tadbir turini tahrirlash">
    <div class="rounded-2xl bg-white p-6 shadow-soft dark:bg-slate-900">
        <form action="{{ route('event-types.update', $eventType) }}" method="POST" class="space-y-4" data-loading-form>
            @csrf @method('PUT')
            @include('categories.form', ['eventType' => $eventType])
            <button type="submit" class="rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Tadbir turini yangilash</button>
        </form>
    </div>
</x-app-layout>

