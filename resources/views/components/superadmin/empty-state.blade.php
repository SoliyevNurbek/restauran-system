@props([
    'icon' => 'inbox',
    'title',
    'description',
    'actionLabel' => null,
    'actionHref' => null,
])

<div {{ $attributes->class('rounded-[28px] border border-dashed border-slate-300 bg-slate-50/80 px-6 py-10 text-center') }}>
    <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-[1.5rem] bg-white text-slate-500 shadow-sm">
        <i data-lucide="{{ $icon }}" class="h-6 w-6"></i>
    </span>
    <h3 class="mt-5 text-lg font-semibold text-slate-950">{{ $title }}</h3>
    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">{{ $description }}</p>
    @if($actionLabel && $actionHref)
        <a href="{{ $actionHref }}" class="mt-5 inline-flex rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white">{{ $actionLabel }}</a>
    @endif
</div>
