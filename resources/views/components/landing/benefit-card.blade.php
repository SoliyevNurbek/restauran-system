@props([
    'title',
    'description',
])

<article {{ $attributes->class('landing-card benefit-card') }}>
    <span class="benefit-card__marker" aria-hidden="true"></span>
    <h3>{{ $title }}</h3>
    <p>{{ $description }}</p>
</article>
