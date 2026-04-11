@props([
    'title',
    'value',
    'icon' => 'bar-chart-3',
    'tone' => 'blue',
    'hint' => null,
])

@php
    $tones = [
        'blue' => 'bg-blue-50 text-blue-700 border-blue-100',
        'green' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
        'amber' => 'bg-amber-50 text-amber-700 border-amber-100',
        'red' => 'bg-rose-50 text-rose-700 border-rose-100',
        'slate' => 'bg-slate-100 text-slate-700 border-slate-200',
    ];
@endphp

<article {{ $attributes->class('sa-card-hover rounded-[28px] border border-slate-200/80 bg-white p-5 shadow-[0_16px_40px_rgba(15,23,42,0.04)]') }}>
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-slate-500">{{ $title }}</p>
            <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">{{ $value }}</p>
            @if($hint)<p class="mt-2 text-sm text-slate-500">{{ $hint }}</p>@endif
        </div>
        <span class="flex h-12 w-12 items-center justify-center rounded-2xl border {{ $tones[$tone] ?? $tones['blue'] }}">
            <i data-lucide="{{ $icon }}" class="h-5 w-5"></i>
        </span>
    </div>
</article>
