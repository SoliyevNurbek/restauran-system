@props([
    'value',
    'title',
    'description',
])

<article {{ $attributes->class('benefit-card premium-card') }}>
    <small>{{ $value }}</small>
    <h3>{{ $title }}</h3>
    <p>{{ $description }}</p>
</article>
