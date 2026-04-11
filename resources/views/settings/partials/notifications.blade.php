<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card title="Signal manbalari" :value="4" icon="bell" />
        <x-stat-card title="Dashboard ogohlantirish" :value="'Faol'" icon="layout-dashboard" />
        <x-stat-card title="Qarzdorlik kuzatuvi" :value="'Faol'" icon="wallet-cards" />
        <x-stat-card title="Ombor eslatmasi" :value="'Faol'" icon="triangle-alert" />
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <x-admin.section-card icon="bell-ring" title="Bildirishnoma yo'nalishlari" subtitle="Tenant panelda ko'rinadigan asosiy ogohlantirish oqimlari.">
            <div class="space-y-3">
                @foreach([
                    ['title' => 'Bugungi tadbirlar', 'text' => 'Dashboard va kalendar bloklarida avtomatik ajratib ko‘rsatiladi.', 'status' => 'Faol'],
                    ['title' => 'Qarzdor bronlar', 'text' => 'Qolgan to‘lovli bronlar moliya va bosh sahifa signalida ko‘rinadi.', 'status' => 'Faol'],
                    ['title' => 'Kam qolgan mahsulotlar', 'text' => 'Minimal limitga tushgan ombor pozitsiyalari tezkor nazoratga chiqadi.', 'status' => 'Faol'],
                    ['title' => 'Yaqin bandlik', 'text' => 'Zallar va tadbir sanalari bo‘yicha yuklama monitoring qilinadi.', 'status' => 'Faol'],
                ] as $item)
                    <div class="flex items-start justify-between gap-4 rounded-2xl border border-slate-100 px-4 py-4 dark:border-slate-800">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $item['title'] }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $item['text'] }}</p>
                        </div>
                        <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">{{ $item['status'] }}</span>
                    </div>
                @endforeach
            </div>
        </x-admin.section-card>

        <x-admin.section-card icon="message-square-more" title="Eslatma va xabar logikasi" subtitle="Tenant panel uchun amaldagi xatti-harakatlar.">
            <div class="grid gap-4">
                <div class="rounded-[28px] border border-amber-200 bg-amber-50 p-5 dark:border-amber-900/50 dark:bg-amber-950/20">
                    <h4 class="text-base font-semibold text-slate-900 dark:text-white">Avtomatik ishlovchi signal tizimi</h4>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Bu bo'lim tenant foydalanuvchiga ko'rinadigan ogohlantirish mantig'ini tartibli ko'rsatadi. Amaldagi signal oqimlari operatsion ma'lumotlardan shakllanadi.</p>
                </div>
                <div class="rounded-[28px] border border-slate-200 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-800/60">
                    <h4 class="text-base font-semibold text-slate-900 dark:text-white">Tavsiya etilgan ish tartibi</h4>
                    <ul class="mt-3 space-y-2 text-sm text-slate-600 dark:text-slate-300">
                        <li>Bron, to'lov va ombor yozuvlarini o'z vaqtida yangilang.</li>
                        <li>Qarzdorlik va bandlik holatini har kuni dashboard orqali tekshirib boring.</li>
                        <li>Yuqori ustuvor signal paydo bo'lsa, tegishli moduldan darhol amal bajaring.</li>
                    </ul>
                </div>
            </div>
        </x-admin.section-card>
    </div>
</div>
