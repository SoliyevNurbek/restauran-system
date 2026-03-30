@props(['status'])

@php
    $styles = [
        'available' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300',
        'unavailable' => 'bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-300',
        'free' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300',
        'occupied' => 'bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300',
        'pending' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
        'preparing' => 'bg-blue-100 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300',
        'served' => 'bg-violet-100 text-violet-700 dark:bg-violet-950/40 dark:text-violet-300',
        'paid' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300',
        'admin' => 'bg-primary-100 text-primary-700 dark:bg-primary-900/40 dark:text-primary-300',
        'waiter' => 'bg-blue-100 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300',
        'cashier' => 'bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300',
    ];

    $labels = [
        'available' => 'Mavjud',
        'unavailable' => 'Mavjud emas',
        'free' => 'Bo\'sh',
        'occupied' => 'Band',
        'pending' => 'Kutilmoqda',
        'preparing' => 'Tayyorlanmoqda',
        'served' => 'Yetkazildi',
        'paid' => 'To\'landi',
        'admin' => 'Admin',
        'waiter' => 'Ofitsiant',
        'cashier' => 'Kassir',
    ];
@endphp

<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $styles[$status] ?? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
    {{ $labels[$status] ?? $status }}
</span>
