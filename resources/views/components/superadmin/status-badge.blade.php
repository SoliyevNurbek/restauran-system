@props([
    'status',
    'label' => null,
])

@php
    $map = [
        'approved' => ['icon' => 'check-circle-2', 'classes' => 'bg-emerald-50 text-emerald-700 ring-emerald-200'],
        'active' => ['icon' => 'check-circle-2', 'classes' => 'bg-emerald-50 text-emerald-700 ring-emerald-200'],
        'paid' => ['icon' => 'badge-check', 'classes' => 'bg-emerald-50 text-emerald-700 ring-emerald-200'],
        'pending' => ['icon' => 'clock-3', 'classes' => 'bg-amber-50 text-amber-700 ring-amber-200'],
        'under_review' => ['icon' => 'search-check', 'classes' => 'bg-blue-50 text-blue-700 ring-blue-200'],
        'trial' => ['icon' => 'sparkles', 'classes' => 'bg-sky-50 text-sky-700 ring-sky-200'],
        'rejected' => ['icon' => 'x-circle', 'classes' => 'bg-rose-50 text-rose-700 ring-rose-200'],
        'failed' => ['icon' => 'x-circle', 'classes' => 'bg-rose-50 text-rose-700 ring-rose-200'],
        'expired' => ['icon' => 'alert-triangle', 'classes' => 'bg-rose-50 text-rose-700 ring-rose-200'],
        'canceled' => ['icon' => 'ban', 'classes' => 'bg-slate-100 text-slate-700 ring-slate-200'],
        'refunded' => ['icon' => 'rotate-ccw', 'classes' => 'bg-fuchsia-50 text-fuchsia-700 ring-fuchsia-200'],
        'suspended' => ['icon' => 'pause-circle', 'classes' => 'bg-slate-100 text-slate-700 ring-slate-200'],
        'warning' => ['icon' => 'shield-alert', 'classes' => 'bg-amber-50 text-amber-700 ring-amber-200'],
        'info' => ['icon' => 'info', 'classes' => 'bg-blue-50 text-blue-700 ring-blue-200'],
    ];
    $config = $map[$status] ?? ['icon' => 'dot', 'classes' => 'bg-slate-100 text-slate-700 ring-slate-200'];
@endphp

<span {{ $attributes->class('inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold ring-1 '.$config['classes']) }}>
    <i data-lucide="{{ $config['icon'] }}" class="h-3.5 w-3.5"></i>
    <span>{{ $label ?? str($status)->replace('_', ' ')->headline() }}</span>
</span>
