@props([
    'icon',
    'title',
    'description',
])

<article {{ $attributes->class('landing-card feature-card') }}>
    <div class="feature-card__icon" aria-hidden="true">{!! $icon !!}</div>
    <h3>{{ $title }}</h3>
    <p>{{ $description }}</p>
</article>
