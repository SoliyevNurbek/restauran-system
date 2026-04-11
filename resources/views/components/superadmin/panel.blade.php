@props([
    'title' => null,
    'subtitle' => null,
    'icon' => 'layout-panel-top',
    'actions' => null,
])

<section {{ $attributes->class('sa-card-hover rounded-[28px] border border-slate-200/80 bg-white p-5 shadow-[0_18px_45px_rgba(15,23,42,0.04)]') }}>
    @if($title || $subtitle || $actions)
        <div class="mb-5 flex flex-wrap items-start justify-between gap-4">
            <div class="flex items-start gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
                    <i data-lucide="{{ $icon }}" class="h-4 w-4"></i>
                </span>
                <div>
                    @if($title)<h2 class="text-base font-semibold text-slate-950">{{ $title }}</h2>@endif
                    @if($subtitle)<p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>@endif
                </div>
            </div>
            @if($actions)<div>{{ $actions }}</div>@endif
        </div>
    @endif
    {{ $slot }}
</section>
