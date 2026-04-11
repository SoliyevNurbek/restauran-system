@props([
    'icon' => 'inbox',
    'title' => "Ma'lumot topilmadi",
    'text' => "Bu bo'limda hali ma'lumot yo'q.",
    'actionHref' => null,
    'actionLabel' => null,
])

<div {{ $attributes->class('rounded-[28px] border border-dashed border-slate-300/80 bg-white/80 px-6 py-10 text-center shadow-soft dark:border-slate-700 dark:bg-slate-900') }}>
    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-300">
        <i data-lucide="{{ $icon }}" class="h-6 w-6"></i>
    </div>
    <h3 class="mt-4 text-lg font-semibold text-slate-900 dark:text-white">{{ $title }}</h3>
    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">{{ $text }}</p>
    @if($actionHref && $actionLabel)
        <a href="{{ $actionHref }}" class="mt-5 inline-flex items-center justify-center rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
            {{ $actionLabel }}
        </a>
    @endif
</div>
