@props([
    'icon',
    'title',
    'description',
    'tone' => 'slate',
])

<article {{ $attributes->class(["feature-card premium-card feature-card--{$tone}"]) }}>
    <div class="feature-card__icon" aria-hidden="true">{!! $icon !!}</div>
    <h3>{{ $title }}</h3>
    <p>{{ $description }}</p>
</article>
