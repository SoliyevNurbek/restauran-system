@props(['title', 'value', 'icon' => 'bar-chart-3'])

<div class="rounded-2xl bg-white p-5 shadow-soft dark:bg-slate-900">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm text-slate-500">{{ $title }}</p>
            <h3 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">{{ $value }}</h3>
        </div>
        <div class="rounded-xl bg-primary-100 p-2 text-primary-700 dark:bg-primary-900/40 dark:text-primary-300">
            <i data-lucide="{{ $icon }}" class="h-5 w-5"></i>
        </div>
    </div>
</div>
