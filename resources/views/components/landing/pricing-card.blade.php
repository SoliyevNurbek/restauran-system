@props([
    'name',
    'price',
    'description',
    'features' => [],
    'highlighted' => false,
    'badge' => null,
])

<article {{ $attributes->class([
    'pricing-card',
    'pricing-card--featured' => $highlighted,
]) }}>
    @if($badge)
        <span class="pricing-card__badge">{{ $badge }}</span>
    @endif

    <div class="pricing-card__head">
        <h3>{{ $name }}</h3>
        <p>{{ $description }}</p>
    </div>

    <div class="pricing-card__price">
        <strong>{{ $price }}</strong>
        <span>oyiga</span>
    </div>

    <ul class="pricing-card__list" role="list">
        @foreach($features as $feature)
            <li>{{ $feature }}</li>
        @endforeach
    </ul>

    <a href="#final-cta" class="button {{ $highlighted ? 'button--primary' : 'button--secondary' }}">Demo bo‘yicha bog‘lanish</a>
</article>
