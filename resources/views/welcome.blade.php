<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Restoran Boshqaruv Tizimi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
    <main class="mx-auto flex min-h-screen max-w-3xl items-center justify-center p-6">
        <div class="w-full rounded-2xl bg-white p-8 shadow">
            <h1 class="text-2xl font-semibold">Restoran Boshqaruv Tizimi</h1>
            <p class="mt-2 text-sm text-slate-600">Tizimga kirish uchun quyidagi tugmadan foydalaning.</p>
            <div class="mt-6 flex gap-3">
                <a href="{{ route('login') }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Kirish</a>
                <a href="{{ route('dashboard') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium hover:bg-slate-50">Bosh sahifa</a>
            </div>
        </div>
    </main>
</body>
</html>

