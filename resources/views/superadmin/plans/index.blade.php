<x-layouts.superadmin :title="$pageTitle" :page-title="$pageTitle" :page-subtitle="$pageSubtitle">
    <div class="grid gap-6 xl:grid-cols-[0.8fr_1.2fr]">
        <x-superadmin.panel title="Yangi tarif" subtitle="Basic, Pro yoki Premium rejasini yarating." icon="layers-3">
            <form method="POST" action="{{ route('superadmin.plans.store') }}" class="space-y-4">
                @csrf
                <div class="grid gap-4 md:grid-cols-2">
                    <input type="text" name="name" placeholder="Plan nomi" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    <input type="text" name="slug" placeholder="Slug" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    <input type="number" min="0" step="0.01" name="amount" placeholder="Narx" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    <input type="number" min="1" name="duration_days" value="30" placeholder="Muddat (kun)" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    <input type="text" name="currency" value="UZS" placeholder="Valyuta" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    <input type="number" min="1" name="display_order" value="1" placeholder="Tartib" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    <select name="billing_cycle" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="yearly">Yearly</option>
                        <option value="manual">Manual</option>
                    </select>
                    <label class="flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300">
                        <span>Faol tarif</span>
                    </label>
                    <div class="md:col-span-2">
                        <input type="text" name="description" placeholder="Qisqa tavsif" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <textarea name="features_text" rows="5" placeholder="Har bir feature yangi qatordan" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm"></textarea>
                    </div>
                </div>
                <button class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Tarif yaratish</button>
            </form>
        </x-superadmin.panel>

        <div class="space-y-6">
            @foreach($plans as $plan)
                <x-superadmin.panel :title="$plan->name" :subtitle="$plan->description ?: 'Tarif tafsilotlari'" icon="badge-check">
                    <form method="POST" action="{{ route('superadmin.plans.update', $plan) }}" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div class="grid gap-4 md:grid-cols-2">
                            <input type="text" name="name" value="{{ $plan->name }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                            <input type="text" name="slug" value="{{ $plan->slug }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                            <input type="number" min="0" step="0.01" name="amount" value="{{ $plan->amount }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                            <input type="number" min="1" name="duration_days" value="{{ $plan->duration_days ?: 30 }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                            <input type="text" name="currency" value="{{ $plan->currency }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                            <input type="number" min="1" name="display_order" value="{{ $plan->display_order }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                            <select name="billing_cycle" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                                @foreach(['monthly', 'quarterly', 'yearly', 'manual'] as $cycle)
                                    <option value="{{ $cycle }}" @selected($plan->billing_cycle === $cycle)>{{ ucfirst($cycle) }}</option>
                                @endforeach
                            </select>
                            <label class="flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                                <input type="checkbox" name="is_active" value="1" @checked($plan->is_active) class="rounded border-slate-300">
                                <span>Faol tarif</span>
                            </label>
                            <div class="md:col-span-2">
                                <input type="text" name="description" value="{{ $plan->description }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                            </div>
                            <div class="md:col-span-2">
                                <textarea name="features_text" rows="4" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">{{ collect($plan->features ?? [])->implode("\n") }}</textarea>
                            </div>
                        </div>
                        <button class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white">Saqlash</button>
                    </form>
                </x-superadmin.panel>
            @endforeach
        </div>
    </div>
</x-layouts.superadmin>
