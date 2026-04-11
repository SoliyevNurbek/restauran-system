@props([
    'quote',
    'author',
    'role',
])

<article {{ $attributes->class('landing-card testimonial-card') }}>
    <p class="testimonial-card__quote">“{{ $quote }}”</p>
    <div class="testimonial-card__meta">
        <strong>{{ $author }}</strong>
        <span>{{ $role }}</span>
    </div>
</article>
