@props([
    'quote',
    'author',
    'role',
])

<article {{ $attributes->class('testimonial-card premium-card') }}>
    <div class="testimonial-card__stars" aria-hidden="true">*****</div>
    <p class="testimonial-card__quote">"{{ $quote }}"</p>
    <div class="testimonial-card__meta">
        <strong>{{ $author }}</strong>
        <span>{{ $role }}</span>
    </div>
</article>
