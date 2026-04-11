<x-layouts.guest title="Superadmin login">
    <div class="flex min-h-screen items-center justify-center bg-slate-950 px-4">
        <form method="POST" action="{{ route('superadmin.login.store') }}" class="w-full max-w-md rounded-3xl border border-slate-800 bg-slate-900 p-8 shadow-2xl">
            @csrf
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-indigo-400">Superadmin</p>
            <h1 class="mt-3 text-3xl font-semibold text-white">Monitoring paneliga kirish</h1>
            <p class="mt-2 text-sm text-slate-400">Landing, approval va monitoring boshqaruvi.</p>
            <div class="mt-6 space-y-4">
                <div><label class="mb-2 block text-sm text-slate-300">Username</label><input name="username" value="{{ old('username') }}" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-white" required></div>
                <div><label class="mb-2 block text-sm text-slate-300">Parol</label><input name="password" type="password" class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-white" required></div>
            </div>
            @error('username')<p class="mt-3 text-sm text-rose-400">{{ $message }}</p>@enderror
            <button type="submit" class="mt-6 w-full rounded-2xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white">Kirish</button>
        </form>
    </div>
</x-layouts.guest>
