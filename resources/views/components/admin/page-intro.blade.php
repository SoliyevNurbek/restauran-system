@props(['eyebrow' => null, 'title', 'subtitle' => null])

<div {{ $attributes->class('flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between') }}>
    <div>
        @if($eyebrow)
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">{{ $eyebrow }}</p>
        @endif
        <h2 class="mt-1 text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">{{ $title }}</h2>
        @if($subtitle)
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">{{ $subtitle }}</p>
        @endif
    </div>
    @if(isset($actions))
        <div>{{ $actions }}</div>
    @endif
</div>
