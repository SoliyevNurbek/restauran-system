@props([
    'eyebrow' => null,
    'title',
    'subtitle' => null,
    'align' => 'left',
])

<div {{ $attributes->class([
    'section-heading',
    'section-heading--center' => $align === 'center',
]) }}>
    @if($eyebrow)
        <span class="section-heading__eyebrow">{{ $eyebrow }}</span>
    @endif

    <h2 class="section-heading__title">{{ $title }}</h2>

    @if($subtitle)
        <p class="section-heading__subtitle">{{ $subtitle }}</p>
    @endif
</div>
