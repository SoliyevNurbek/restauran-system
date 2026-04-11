@props(['title' => null, 'subtitle' => null, 'icon' => null])

<section {{ $attributes->class('rounded-[30px] border border-slate-200/80 bg-white p-5 shadow-soft dark:border-slate-800 dark:bg-slate-900') }}>
    @if($title || $subtitle)
        <div class="mb-5 flex items-start justify-between gap-3">
            <div class="flex items-start gap-3">
                @if($icon)
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                        <i data-lucide="{{ $icon }}" class="h-5 w-5"></i>
                    </span>
                @endif
                <div>
                    @if($title)
                        <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ $title }}</h3>
                    @endif
                    @if($subtitle)
                        <p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
            @if(isset($actions))
                <div>{{ $actions }}</div>
            @endif
        </div>
    @endif

    {{ $slot }}
</section>
