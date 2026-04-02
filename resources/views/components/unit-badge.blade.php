@props([
    'value',
])

<span {{ $attributes->class('inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium uppercase tracking-[0.12em] text-slate-600 dark:bg-slate-800 dark:text-slate-300') }}>
    {{ $value }}
</span>
