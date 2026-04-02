@props([
    'value' => 0,
    'decimals' => 2,
    'suffixClass' => 'text-xs font-medium text-slate-400 dark:text-slate-500',
    'showSuffix' => true,
])

<span {{ $attributes->class('inline-flex items-baseline gap-1 whitespace-nowrap') }}>
    <span>{{ number_format((float) $value, (int) $decimals, '.', ' ') }}</span>
    @if($showSuffix)
        <span class="{{ $suffixClass }}">so'm</span>
    @endif
</span>
