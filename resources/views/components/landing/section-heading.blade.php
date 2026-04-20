@props([
    'eyebrow' => null,
    'title',
    'subtitle' => null,
    'align' => 'left',
])

<div {{ $attributes->class([
    'landing-section-heading',
    'landing-section-heading--center' => $align === 'center',
]) }}>
    @if ($eyebrow)
        <span class="landing-section-heading__eyebrow">{{ $eyebrow }}</span>
    @endif

    <h2 class="landing-section-heading__title">{{ $title }}</h2>

    @if ($subtitle)
        <p class="landing-section-heading__subtitle">{{ $subtitle }}</p>
    @endif
</div>
