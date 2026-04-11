@props(['status'])

@php
    $styles = [
        'Faol' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300',
        'Nofaol' => 'bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-300',
        "Ta'mirda" => 'bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300',
        'Yangi' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
        "Yangi so'rov" => 'bg-sky-100 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300',
        'Tasdiqlangan' => 'bg-blue-100 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300',
        'Avans olingan' => 'bg-cyan-100 text-cyan-700 dark:bg-cyan-950/40 dark:text-cyan-300',
        'Tayyorlanmoqda' => 'bg-violet-100 text-violet-700 dark:bg-violet-950/40 dark:text-violet-300',
        "Tadbir bo'lib o'tdi" => 'bg-fuchsia-100 text-fuchsia-700 dark:bg-fuchsia-950/40 dark:text-fuchsia-300',
        'Yakunlandi' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300',
        'Otkazildi' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300',
        'Bekor qilindi' => 'bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-300',
        "To'langan" => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300',
        "Qisman to'langan" => 'bg-blue-100 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300',
        'Kutilmoqda' => 'bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300',
        'Kechiktirilgan' => 'bg-orange-100 text-orange-700 dark:bg-orange-950/40 dark:text-orange-300',
        'Administrator' => 'bg-primary-100 text-primary-700 dark:bg-primary-900/40 dark:text-primary-300',
        'Menejer' => 'bg-blue-100 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300',
        'Kassir' => 'bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300',
        'Oshpaz' => 'bg-orange-100 text-orange-700 dark:bg-orange-950/40 dark:text-orange-300',
        'Ofitsiant' => 'bg-sky-100 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300',
        'Dekorator' => 'bg-pink-100 text-pink-700 dark:bg-pink-950/40 dark:text-pink-300',
        'Texnik xodim' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
    ];
@endphp

<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $styles[$status] ?? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
    {{ $status }}
</span>

