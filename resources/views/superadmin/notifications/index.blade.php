<x-layouts.superadmin :title="$pageTitle" :page-title="$pageTitle" :page-subtitle="$pageSubtitle">
    <x-superadmin.panel title="Notification center" subtitle="Unread/read holati, type filter va action flow." icon="bell">
        <div class="mb-5 flex flex-wrap gap-2">
            <a href="{{ route('superadmin.notifications.index') }}" class="rounded-2xl px-4 py-2 text-sm font-semibold {{ $type === '' ? 'bg-slate-950 text-white' : 'border border-slate-200 bg-white text-slate-700' }}">Barchasi</a>
            @foreach($types as $item)
                <a href="{{ route('superadmin.notifications.index', ['type' => $item]) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold {{ $type === $item ? 'bg-slate-950 text-white' : 'border border-slate-200 bg-white text-slate-700' }}">{{ str($item)->replace('_', ' ')->headline() }}</a>
            @endforeach
        </div>
        @forelse($notifications as $notification)
            <div class="flex flex-col gap-4 rounded-[24px] border border-slate-200 p-5 {{ !$loop->last ? 'mb-4' : '' }} lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-start gap-4">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
                        <i data-lucide="{{ $notification->icon }}" class="h-5 w-5"></i>
                    </span>
                    <div>
                        <div class="flex flex-wrap items-center gap-3">
                            <h3 class="font-semibold text-slate-900">{{ $notification->title }}</h3>
                            <x-superadmin.status-badge :status="$notification->status" />
                            @unless($notification->is_read)
                                <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700 ring-1 ring-sky-200">Unread</span>
                            @endunless
                        </div>
                        <p class="mt-1 text-sm text-slate-500">{{ $notification->description }}</p>
                        <p class="mt-2 text-xs text-slate-400">{{ optional($notification->occurred_at)->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    @if($notification->action_url)
                        <a href="{{ $notification->action_url }}" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700">Open</a>
                    @endif
                    <form method="POST" action="{{ route('superadmin.notifications.update', $notification) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="is_read" value="{{ $notification->is_read ? 0 : 1 }}">
                        <button class="rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white">{{ $notification->is_read ? 'Unread' : 'Mark as read' }}</button>
                    </form>
                </div>
            </div>
        @empty
            <x-superadmin.empty-state icon="bell-off" title="Bildirishnomalar yo'q" description="Muhim platforma eventlari paydo bo'lganda notification center shu yerda to'ladi." />
        @endforelse
    </x-superadmin.panel>

    <div class="mt-6">{{ $notifications->links() }}</div>
</x-layouts.superadmin>
