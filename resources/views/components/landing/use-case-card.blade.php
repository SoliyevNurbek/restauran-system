@props([
    'title',
    'description',
    'tag',
])

<article {{ $attributes->class('landing-card use-case-card') }}>
    <span class="use-case-card__tag">{{ $tag }}</span>
    <h3>{{ $title }}</h3>
    <p>{{ $description }}</p>
</article>
