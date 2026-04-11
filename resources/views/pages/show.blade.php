@php
    $backUrl = url()->previous() !== url()->current() ? url()->previous() : route('register');
@endphp
<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->title }}</title>
    <style>
        body { margin: 0; font-family: Inter, Arial, sans-serif; color: #17212b; background: #f5f7fb; }
        .wrap { max-width: 900px; margin: 0 auto; padding: 32px 20px 56px; }
        .card { background: #fff; border: 1px solid #dbe3ee; border-radius: 24px; padding: 28px; box-shadow: 0 20px 60px rgba(15, 23, 42, .08); }
        .meta { color: #64748b; font-size: .95rem; margin-bottom: 18px; }
        .title { margin: 0 0 10px; font-size: clamp(1.8rem, 4vw, 2.6rem); line-height: 1.1; }
        .content { color: #334155; line-height: 1.8; white-space: pre-wrap; }
        .back { display: inline-flex; margin-bottom: 16px; color: #1d4ed8; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="wrap">
        <a href="{{ $backUrl }}" class="back">Ortga</a>
        <article class="card">
            <p class="meta">Versiya {{ $page->version }} • Yangilangan {{ optional($page->published_at ?? $page->updated_at)->format('d.m.Y H:i') }}</p>
            <h1 class="title">{{ $page->title }}</h1>
            <div class="content">{{ $page->content }}</div>
        </article>
    </div>
</body>
</html>
