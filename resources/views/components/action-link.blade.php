@props([
    'href' => '#',
    'icon' => 'square',
    'variant' => 'neutral',
])

@php
    $variants = [
        'view' => 'border border-sky-200 bg-sky-50 text-sky-700 dark:border-sky-900/50 dark:bg-sky-950/20 dark:text-sky-300',
        'edit' => 'border border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-900/50 dark:bg-amber-950/20 dark:text-amber-300',
        'neutral' => 'border border-slate-200 bg-white text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200',
    ];
@endphp

<a href="{{ $href }}" aria-label="{{ trim(strip_tags((string) $slot)) }}" {{ $attributes->class([
    'inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-medium transition duration-200 hover:-translate-y-0.5 hover:shadow-sm',
    $variants[$variant] ?? $variants['neutral'],
]) }}>
    <i data-lucide="{{ $icon }}" class="h-3.5 w-3.5"></i>
    <span>{{ $slot }}</span>
</a>
