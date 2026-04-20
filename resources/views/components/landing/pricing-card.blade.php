@props([
    'name',
    'price',
    'period' => '/oy',
    'description' => null,
    'features' => [],
    'highlighted' => false,
    'badge' => null,
    'ctaText' => 'Bepul demo olish',
])

<article {{ $attributes->class([
    'pricing-card premium-card',
    'pricing-card--featured' => $highlighted,
]) }}>
    @if ($badge)
        <span class="pricing-card__badge">{{ $badge }}</span>
    @endif

    <div class="pricing-card__head">
        <h3>{{ $name }}</h3>
        @if ($description)
            <p>{{ $description }}</p>
        @endif
    </div>

    <div class="pricing-card__price">
        <strong>{{ $price }}</strong>
        <span>{{ $period }}</span>
    </div>

    <ul class="pricing-card__list" role="list">
        @foreach ($features as $feature)
            <li>{{ $feature }}</li>
        @endforeach
    </ul>

    <a href="#contact" class="button {{ $highlighted ? 'button--primary' : 'button--ghost' }}">{{ $ctaText }}</a>
</article>
