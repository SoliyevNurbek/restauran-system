<x-layouts.superadmin :title="$pageTitle" :page-title="$pageTitle" :page-subtitle="$pageSubtitle">
    <x-superadmin.panel title="Audit timeline" subtitle="Actor, action, target va before/after snapshot." icon="history">
        @forelse($logs as $log)
            <div class="rounded-[24px] border border-slate-200 p-5 {{ !$loop->last ? 'mb-4' : '' }}">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="font-semibold text-slate-900">{{ $log->action }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $log->actor?->name ?? 'System' }} Р’ -  {{ $log->target_label ?? $log->target_type }}</p>
                    </div>
                    <x-superadmin.status-badge :status="$log->severity" />
                </div>
                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    <div class="rounded-2xl bg-slate-50 px-4 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Before</p>
                        <pre class="mt-3 whitespace-pre-wrap text-xs text-slate-600">{{ json_encode($log->before, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: 'РІР‚"' }}</pre>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">After</p>
                        <pre class="mt-3 whitespace-pre-wrap text-xs text-slate-600">{{ json_encode($log->after, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: 'РІР‚"' }}</pre>
                    </div>
                </div>
                <p class="mt-3 text-xs text-slate-400">{{ $log->created_at?->format('d.m.Y H:i') }} Р’ -  {{ $log->ip ?: 'IP yoРІР‚Вq' }}</p>
            </div>
        @empty
            <x-superadmin.empty-state icon="history" title="Audit loglari boРІР‚Вsh" description="Muhim superadmin harakatlari bajarilganda audit trail shu yerda koРІР‚Вrinadi." />
        @endforelse
    </x-superadmin.panel>

    <div class="mt-6">{{ $logs->links() }}</div>
</x-layouts.superadmin>
