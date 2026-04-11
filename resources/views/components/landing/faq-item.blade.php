@props([
    'question',
    'answer',
    'open' => false,
])

<article class="faq-item" data-faq-item>
    <button
        class="faq-item__question"
        type="button"
        aria-expanded="{{ $open ? 'true' : 'false' }}"
        data-faq-trigger
    >
        <span>{{ $question }}</span>
        <span class="faq-item__icon" aria-hidden="true"></span>
    </button>

    <div class="faq-item__answer" @if(!$open) hidden @endif data-faq-panel>
        <p>{{ $answer }}</p>
    </div>
</article>
