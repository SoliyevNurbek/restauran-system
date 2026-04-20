@php
use App\Models\LandingContent;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

$locale = in_array(request('lang', 'uz'), ['uz', 'uzc', 'ru', 'en'], true) ? request('lang', 'uz') : 'uz';
$fallbackLocale = 'uz';
$resolvedSetting = Schema::hasTable('settings') ? \App\Models\Setting::global() : null;
$resolvedMediaAssets = Schema::hasTable('media_assets') ? \App\Models\MediaAsset::keyed() : collect();
$brandName = $resolvedSetting?->restaurant_name ?: 'MyRestaurant_SN';
$brandLogo = $resolvedMediaAssets->get('brand_logo');
$fallbackPack = $languageLines->get($fallbackLocale, collect());
$langPack = $languageLines->get($locale, collect());
$langText = static fn (string $key, string $default) => filled($langPack->get($key))
    ? $langPack->get($key)
    : (filled($fallbackPack->get($key)) ? $fallbackPack->get($key) : $default);

$loginUrl = Route::has('login') ? route('login', ['lang' => $locale]) : '#';
$registerUrl = Route::has('register') ? route('register', ['lang' => $locale]) : '#';
$landingUrl = Route::has('landing') ? route('landing', ['lang' => $locale]) : url('/');

$formatMoney = static function (float|int|string $amount, string $currency = 'UZS'): string {
    $amount = (float) $amount;

    return match (strtoupper($currency)) {
        'USD' => '$'.number_format($amount, 1),
        'EUR' => 'EUR '.number_format($amount, 1),
        default => number_format($amount, 0, '.', ' ').' '.strtoupper($currency),
    };
};

$formatPlanPeriod = static function (?string $billingCycle, ?int $durationDays): string {
    $days = max((int) $durationDays, 1);

    return match ($billingCycle) {
        'yearly' => '/yil',
        'quarterly' => '/chorak',
        'manual' => "/{$days} kun",
        default => '/oy',
    };
};

$iconSvgs = [
    'calendar' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 2V5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/><path d="M16 2V5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/><path d="M3.5 9.25H20.5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/><rect x="3" y="4.5" width="18" height="16.5" rx="3" stroke="currentColor" stroke-width="1.75"/><path d="M8 13H8.01" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/><path d="M12 13H12.01" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/><path d="M16 13H16.01" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>',
    'users' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="9" cy="8" r="4" stroke="currentColor" stroke-width="1.75"/><path d="M3.5 19.5C3.5 16.7386 5.73858 14.5 8.5 14.5H9.5C12.2614 14.5 14.5 16.7386 14.5 19.5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/><path d="M16 4.75C18.0216 5.27166 19.5 7.1082 19.5 9.25C19.5 11.3918 18.0216 13.2283 16 13.75" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/><path d="M16.5 19.5C16.5 17.5091 15.3245 15.7928 13.6289 15" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/></svg>',
    'wallet' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7.5C4 5.84315 5.34315 4.5 7 4.5H18.5C19.8807 4.5 21 5.61929 21 7V17C21 18.3807 19.8807 19.5 18.5 19.5H7C5.34315 19.5 4 18.1569 4 16.5V7.5Z" stroke="currentColor" stroke-width="1.75"/><path d="M16.75 12H16.76" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/><path d="M4.5 8.5H20.5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/></svg>',
    'boxes' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3L19 7L12 11L5 7L12 3Z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/><path d="M5 7V17L12 21L19 17V7" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/><path d="M12 11V21" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/></svg>',
    'chart' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 19.5H20" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/><path d="M7 15.5L10.25 11.75L13.5 13.75L18 8.5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 8.5H18V11.5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    'layout' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="3" stroke="currentColor" stroke-width="1.75"/><path d="M9 4V20" stroke="currentColor" stroke-width="1.75"/><path d="M9 10H21" stroke="currentColor" stroke-width="1.75"/></svg>',
    'send' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M21 3L10.5 13.5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/><path d="M21 3L14 20L10.5 13.5L4 10L21 3Z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/></svg>',
];

$content = [
    'meta_title' => "{$brandName} | To'yxona boshqaruvi uchun premium SaaS",
    'meta_description' => "{$brandName} - to'yxona va banket zallari uchun bron, CRM, moliya, ombor va analitika platformasi.",
    'tagline' => "To'yxona biznesi uchun premium boshqaruv platformasi",
    'nav' => [
        ['href' => '#features', 'label' => 'Imkoniyatlar'],
        ['href' => '#pricing', 'label' => 'Narxlar'],
        ['href' => '#demo', 'label' => 'Demo'],
    ],
    'hero' => [
        'badge' => 'Wedding hall management SaaS',
        'title' => "To'yxonangizni raqamlashtiring va daromadingizni 2x oshiring",
        'text' => "Bronlar, mijozlar va moliyani bitta tizimda boshqaring - hech qanday chalkashliksiz.",
        'support' => "Premium darajadagi nazorat, tezkor qarorlar va real vaqt ko'rinishi bir platformada.",
        'primary' => 'Bepul demo olish',
        'secondary' => '7 kun bepul sinov',
        'tertiary' => 'Tizimni ulash',
        'trust' => ['Bronlar markazlashadi', "Kalendar xatosiz ishlaydi", "Rahbar uchun realtime nazorat"],
        'video' => [
            'label' => '30 soniyalik product demo',
            'title' => "30 soniyada tizim qanday ishlashini ko'ring",
            'text' => "To'yxona egasi, administrator va menejer uchun eng muhim oqimlar bitta qisqa preview ichida.",
            'duration' => '00:30',
            'cta' => "Demo ko'rish",
            'embed' => null,
        ],
    ],
    'stats' => [
        ['120+', 'Faol zallar'],
        ['18k', 'Oylik bron'],
        ['99.9%', "To'lov nazorati"],
    ],
    'problem' => [
        'title' => 'Muammolar sizga tanishmi?',
        'subtitle' => 'Bu muammolar sizga oyiga millionlab zarar keltirishi mumkin',
        'items' => [
            ['Bronlar chalkashib ketadi', "Bir sanaga bir nechta so'rov tushadi va jamoa xatoni juda kech payqaydi."],
            ['Band kunlarni topish qiyin', "Bo'sh va band sanalar tez ko'rinmasa, savdo jarayoni sekinlashadi."],
            ["Pul oqimi ko'rinmaydi", "Avans, yakuniy to'lov va qarzdorlik alohida yuritilgani uchun nazorat yo'qoladi."],
            ['Adminlar xato qiladi', "Qo'lda yuritilgan operatsiya ortidan mijoz, sana va summa bo'yicha xatolar ko'payadi."],
        ],
    ],
    'transformation' => [
        'title' => "MyRestaurant_SN bilan hammasi o'zgaradi",
        'subtitle' => "Chaos -> control o'tishi har bir zal, har bir bron va har bir so'm darajasida ko'rinadi.",
        'items' => [
            ["Tartibsiz bronlar", "To'liq avtomatlashtirilgan tizim"],
            ["Qo'lda hisob", "Real vaqt analytics"],
            ["Yo'qotilgan mijozlar", "Har bir mijoz nazoratda"],
        ],
    ],
    'solution' => [
        'title' => "MyRestaurant_SN bilan hammasi o'zgaradi",
        'subtitle' => "Zallar boshqaruvi, bronlar, narx siyosati va CRM oqimi bitta premium boshqaruv qatlamiga birlashadi.",
        'items' => [
            ["Zallar boshqaruvi moduli", "Har bir zal bo'yicha alohida ishlash tartibi va operatsion nazorat."],
            ['Har bir zal uchun kalendar', "Kun, hafta va oy kesimida bo'shliq va bandlik aniq ko'rinadi."],
            ["Bronlarni avtomatik boshqarish", "So'rov, tasdiqlash, avans va yopilish bir jarayon sifatida yuradi."],
            ["Band sanalarni ko'rish", "Savdo jamoasi kerakli sanani bir qarashda topadi."],
            ['Narxlarni sozlash', "Sana, mavsum va zal bo'yicha narx strategiyasini boshqaring."],
        ],
    ],
    'benefits' => [
        'title' => 'Tizim siz uchun nima qiladi?',
        'subtitle' => "Foyda faqat chiroyli interfeysda emas, balki daromad, intizom va nazoratda ko'rinadi.",
        'items' => [
            ['+30%', "Ko'proq bronlar", "Bo'sh kunlarni tezroq to'ldirish va har bir leadni yo'qotmaslik uchun."],
            ['0 xato', 'Xatolarsiz kalendar', "Zallar, band sanalar va bron statuslari yagona oqimda yuradi."],
            ['24/7', 'Daromad nazorati', "Tushum, qarzdorlik va to'lovlar holati doim ko'z oldingizda bo'ladi."],
        ],
    ],
    'audience' => [
        'title' => "To'yxona egasi, administrator va menejer uchun birdek qulay",
        'subtitle' => "Har bir rol tizimdan o'z foydasini birinchi daqiqadanoq ko'rishi kerak - shundagina registratsiya qarori tezlashadi.",
        'items' => [
            ['To\'yxona egalari uchun', 'Daromad, bandlik va operatsion intizomni yuqori darajada nazorat qiling.', ['Rahbar uchun realtime dashboard', 'Bo\'sh kunlar va tushum yo\'qotilishini ko\'ring', 'Qarorlarni analytics asosida qabul qiling']],
            ['Administratorlar uchun', 'Bron va kalendar bilan ishlashni tez, aniq va xatolarsiz qiling.', ['Bir oynada barcha bronlar', 'Band sanalar chalkashmaydi', 'Mijoz bilan ishlash tartibli bo\'ladi']],
            ['Menejerlar uchun', 'Lead, CRM va sotuv jarayonini nazorat ostida ushlab, ko\'proq bron yoping.', ['Har bir so\'rov pipeline\'da ko\'rinadi', 'Qayta aloqa va follow-up tezlashadi', 'Savdo jarayoni tizimli yuradi']],
        ],
    ],
    'features' => [
        'title' => 'Platforma imkoniyatlari',
        'subtitle' => "Kundalik operatsiyadan rahbar darajasidagi qarorgacha bo'lgan butun boshqaruv sikli uchun.",
        'items' => [
            ['calendar', 'Bron tizimi', "Band sanalarni, navbatni va bron pipeline'ni markazdan boshqaring.", 'emerald'],
            ['users', 'CRM', "Mijoz tarixi, kelishuv bosqichi va aloqa holatini yo'qotmang.", 'slate'],
            ['wallet', "Moliyaviy nazorat", "Avans, to'liq to'lov va qarzdorlikni bitta paneldan kuzating.", 'teal'],
            ['boxes', 'Ombor', "Mahsulot, sarf-harajat va tayyorlov oqimini tizimli usulda yuriting.", 'amber'],
            ['chart', 'Analytics', "Bandlik, tushum va samaradorlik bo'yicha tezkor qarorlar qabul qiling.", 'violet'],
            ['layout', 'Admin panel', "Turli rollar uchun tartibli va professional boshqaruv interfeysi.", 'slate'],
            ['send', 'Telegram integratsiya', "Muhim jarayonlar va statuslar bo'yicha tezkor xabarnoma oling.", 'emerald'],
        ],
    ],
    'urgency' => [
        'title' => "Har bir bo'sh kun - bu yo'qotilgan daromad",
        'text' => "MyRestaurant_SN yordamida siz yo'qotilayotgan bronlarni qaytarasiz, savdo oqimini tezlashtirasiz va daromadingizni oshirasiz.",
        'highlight' => "+30% ko'proq bronlar",
    ],
    'testimonials' => [
        'title' => 'Bizga ishonayotganlar',
        'items' => [
            ['Bu tizim bilan bronlarni boshqarish 3x osonlashdi.', 'Javohir', 'Wedding Hall egasi'],
            ["Endi xatolar yo'q, hammasi tizimli.", 'Nilufar A.', 'Administrator'],
            ["Mijozlar bilan ishlash tezlashdi, jamoani nazorat qilish ham ancha qulaylashdi.", 'Sardor M.', 'Boshqaruvchi'],
        ],
    ],
    'pricing' => [
        'title' => 'Oddiy va tushunarli narxlar',
        'subtitle' => "To'yxona hajmi va boshqaruv murakkabligiga qarab o'sadigan tariflar.",
        'popular' => 'Eng ommabop',
    ],
    'objections' => [
        'title' => 'Nega hozir boshlash kerak?',
        'subtitle' => "Registratsiyani ortga surish odatda daromad, lead va operatsion nazorat yo'qotilishi degani.",
        'items' => [
            ['Joriy jarayonni buzib yubormaydimi?', "Yo'q. Bosqichma-bosqich onboarding qilinadi, jamoa parallel o'rganadi."],
            ['Jamoa ishlata oladimi?', "Ha. Administrator, menejer va rahbar uchun interfeys oddiy, ammo professional ko'rinishda qurilgan."],
            ['Natija qachon seziladi?', "Birinchi haftadanoq band sanalar, lead holati va to'lov nazorati tartibga tushadi."],
        ],
        'trust' => [
            "Demo bilan ko'rsatib beramiz",
            'Registratsiya va onboarding tez',
            'Jamoa uchun amaliy moslashuv bor',
        ],
    ],
    'final' => [
        'title' => 'Tizimni ulashni boshlang',
        'text' => "To'yxonangizni boshqarishni professional darajaga olib chiqing. Bugunoq boshlang - ertaga kech bo'lishi mumkin.",
        'buttons' => ['Bepul demo olish', '7 kunlik bepul sinov', 'Konsultatsiya', "Ro'yxatdan o'tish", "Demo so'rash"],
        'points' => ['3 daqiqada registratsiya', 'Jamoa bilan tez ishga tushish', 'Demo va onboarding yordami mavjud'],
    ],
    'contact' => [
        'title' => "Jamoamiz bilan bog'laning",
        'text' => 'Demo yoki savollar uchun tezda chiqamiz.',
        'items' => [
            ['Telefon', $resolvedSetting?->contact_phone ?: '+998937394243', 'tel:'.preg_replace('/[^0-9+]/', '', $resolvedSetting?->contact_phone ?: '+998937394243')],
            ['Telegram', '@SoliyevNurbek', 'https://t.me/SoliyevNurbek'],
            ['Jamoa Telegram', '@MyRestaurant_SN', 'https://t.me/MyRestaurant_SN'],
        ],
    ],
];

$contentOverride = Schema::hasTable('landing_contents')
    ? LandingContent::query()->where('locale', $locale)->first()
    : null;

if ($contentOverride) {
    $content['hero']['badge'] = $contentOverride->hero_badge ?: $content['hero']['badge'];
    $content['hero']['title'] = $contentOverride->hero_title ?: $content['hero']['title'];
    $content['hero']['text'] = $contentOverride->hero_text ?: $content['hero']['text'];
    $content['hero']['primary'] = $contentOverride->hero_primary_cta ?: $content['hero']['primary'];
    $content['hero']['secondary'] = $contentOverride->hero_secondary_cta ?: $content['hero']['secondary'];
    $content['final']['title'] = $contentOverride->final_title ?: $content['final']['title'];
    $content['final']['text'] = $contentOverride->final_text ?: $content['final']['text'];
    $content['contact']['title'] = $contentOverride->contact_title ?: $content['contact']['title'];
    $content['contact']['text'] = $contentOverride->contact_text ?: $content['contact']['text'];
}

$content['meta_title'] = str_replace('MyRestaurant_SN', $brandName, $langText('landing_meta_title', $content['meta_title']));
$content['meta_description'] = str_replace('MyRestaurant_SN', $brandName, $langText('landing_meta_description', $content['meta_description']));
$content['hero']['badge'] = $langText('landing_hero_badge', $content['hero']['badge']);
$content['hero']['title'] = str_replace('MyRestaurant_SN', $brandName, $langText('landing_hero_title', $content['hero']['title']));
$content['hero']['text'] = str_replace('MyRestaurant_SN', $brandName, $langText('landing_hero_text', $content['hero']['text']));
$content['hero']['primary'] = $langText('landing_hero_primary_cta', $content['hero']['primary']);
$content['hero']['secondary'] = $langText('landing_hero_secondary_cta', $content['hero']['secondary']);
$content['problem']['title'] = $langText('landing_problem_title', $content['problem']['title']);
$content['problem']['subtitle'] = $langText('landing_problem_note', $content['problem']['subtitle']);
$content['pricing']['title'] = $langText('landing_pricing_title', $content['pricing']['title']);
$content['final']['title'] = $langText('landing_final_title', $content['final']['title']);
$content['final']['text'] = str_replace('MyRestaurant_SN', $brandName, $langText('landing_final_text', $content['final']['text']));
$content['contact']['title'] = $langText('landing_contact_title', $content['contact']['title']);
$content['contact']['text'] = $langText('landing_contact_text', $content['contact']['text']);

$pricingPlans = [
    ['Basic', '490 000 UZS', '/oy', '1 venue uchun tez ishga tushadigan asosiy paket.', ['1 venue', 'Booking dashboard', 'Basic notifications'], false],
    ['Pro', '990 000 UZS', '/oy', "Faol to'yxonalar uchun chuqurroq nazorat va analytics.", ['Advanced analytics', 'Priority support', 'Multi-hall insights'], true],
    ['Premium', '2 490 000 UZS', '/oy', "Katta operatsiya va rahbarlar uchun to'liq premium oqim.", ['Dedicated onboarding', 'Custom workflows', 'Executive reporting'], false],
];

if (Schema::hasTable('subscription_plans')) {
    $dbPlans = SubscriptionPlan::query()
        ->where('is_active', true)
        ->orderBy('display_order')
        ->get();

    if ($dbPlans->isNotEmpty()) {
        $pricingPlans = $dbPlans->map(function (SubscriptionPlan $plan) use ($formatMoney, $formatPlanPeriod) {
            $features = collect($plan->features)->filter()->take(6)->values()->all();

            return [
                $plan->name,
                $formatMoney($plan->amount, $plan->currency ?: 'UZS'),
                $formatPlanPeriod($plan->billing_cycle, $plan->duration_days),
                $plan->description ?: '',
                $features,
                $plan->slug === 'pro',
            ];
        })->all();
    }
}

$heroMetrics = [
    ['Oylik tushum', '$48.7k', '+18.4%', 'emerald'],
    ['Bandlik', '82%', '+12%', 'teal'],
    ['Qarzdorlik', '$4.2k', '-21%', 'violet'],
];

$pipeline = [
    ['Lead', 24],
    ['Muzokara', 18],
    ['Shartnoma', 11],
    ["To'lov", 8],
];

$events = [
    ['Grand Samarkand Hall', '12 May', 'Tasdiqlangan'],
    ['Nihol Wedding', '14 May', 'Avans olindi'],
    ['Crystal Venue', '16 May', 'Muzokarada'],
];

$bookingBoard = [
    ['Hall A', '10-12 May', 'Band', 'danger'],
    ['Hall B', '13 May', "Bo'sh", 'success'],
    ['Hall C', '14-16 May', 'Bron jarayonda', 'warning'],
    ['VIP Hall', '18 May', 'Band', 'danger'],
];

$showcaseModules = [
    ['title' => 'CRM + Booking', 'text' => "Har bir leadning keyingi qadami aniq."],
    ['title' => 'Revenue visibility', 'text' => "Avans va final to'lovlar uzilmaydi."],
    ['title' => 'Hall calendar', 'text' => "Band kunlar bir qarashda ko'rinadi."],
];

$calculatorPlans = collect($pricingPlans)->map(function (array $plan) {
    return [
        'name' => $plan[0],
        'amount' => (int) preg_replace('/[^0-9]/', '', $plan[1]),
        'highlighted' => (bool) $plan[5],
    ];
})->values();

$demoFunnelSteps = [
    [
        'eyebrow' => '1-qadam',
        'title' => 'Rolingizni tanlang',
        'text' => "Sizga mos demo oqimini tanlash uchun avval jamoa ichidagi vazifangizni belgilang.",
        'choices' => [
            ['value' => 'owner', 'label' => "To'yxona egasi"],
            ['value' => 'admin', 'label' => 'Administrator'],
            ['value' => 'manager', 'label' => 'Menejer'],
        ],
    ],
    [
        'eyebrow' => '2-qadam',
        'title' => 'Operatsiya hajmini belgilang',
        'text' => "Zallar soni va kiruvchi bron oqimi bo'yicha tizim yuklamasini tanlang.",
        'choices' => [
            ['value' => 'compact', 'label' => '1-2 zal'],
            ['value' => 'growth', 'label' => '3-4 zal'],
            ['value' => 'scale', 'label' => '5+ zal'],
        ],
    ],
    [
        'eyebrow' => '3-qadam',
        'title' => 'Qachon start bermoqchisiz?',
        'text' => "Shu ma'lumot asosida demo, onboarding va registratsiya yo'lini qisqartiramiz.",
        'choices' => [
            ['value' => 'now', 'label' => 'Shu hafta'],
            ['value' => 'month', 'label' => 'Shu oy'],
            ['value' => 'later', 'label' => 'Ko\'rib chiqyapman'],
        ],
    ],
];
@endphp

<x-layouts.landing :title="$content['meta_title']" :description="$content['meta_description']">
    <div class="landing-shell">
        <div class="grid-guide" aria-hidden="true"></div>
        <div class="ambient ambient--one" aria-hidden="true"></div>
        <div class="ambient ambient--two" aria-hidden="true"></div>
        <div class="ambient ambient--three" aria-hidden="true"></div>

        <header class="site-header" data-site-header>
            <div class="container site-header__inner">
                <a href="{{ $landingUrl }}" class="brand">
                    <span class="brand__mark">
                        @if($brandLogo?->url())
                            <img src="{{ $brandLogo->url() }}" alt="{{ $brandName }}">
                        @else
                            <strong>{{ strtoupper(substr($brandName, 0, 2)) }}</strong>
                        @endif
                    </span>
                    <span class="brand__copy">
                        <strong>{{ $brandName }}</strong>
                        <small>{{ $content['tagline'] }}</small>
                    </span>
                </a>

                <nav class="site-nav" data-mobile-nav>
                    @foreach ($content['nav'] as $item)
                        <a href="{{ $item['href'] }}">{{ $item['label'] }}</a>
                    @endforeach
                </nav>

                <div class="site-actions">
                    <a href="{{ $loginUrl }}" class="button button--ghost">{{ $langText('landing_nav_login', 'Kirish') }}</a>
                    <a href="{{ $registerUrl }}" class="button button--primary">{{ $langText('landing_nav_register', "Ro'yxatdan o'tish") }}</a>
                </div>

                <button class="mobile-toggle" type="button" aria-expanded="false" aria-label="Toggle menu" data-mobile-toggle>
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </header>

        <main>
            <section class="hero-section">
                <div class="container hero-grid">
                    <div class="hero-copy" data-reveal>
                        <span class="hero-kicker">{{ $content['hero']['badge'] }}</span>
                        <h1>{{ $content['hero']['title'] }}</h1>
                        <p class="hero-lead">{{ $content['hero']['text'] }}</p>
                        <p class="hero-support">{{ $content['hero']['support'] }}</p>

                        <div class="hero-actions">
                            <a href="#contact" class="button button--primary button--large">{{ $content['hero']['primary'] }}</a>
                            <a href="#final-cta" class="button button--secondary button--large">{{ $content['hero']['secondary'] }}</a>
                            <a href="{{ $registerUrl }}" class="button button--ghost button--large">{{ $content['hero']['tertiary'] }}</a>
                        </div>

                        <div class="hero-trust">
                            @foreach ($content['hero']['trust'] as $item)
                                <span>{{ $item }}</span>
                            @endforeach
                        </div>

                        <div class="hero-proof-strip premium-card" data-reveal>
                            <div class="hero-proof-strip__item">
                                <small>Control layer</small>
                                <strong>Owners</strong>
                            </div>
                            <div class="hero-proof-strip__item">
                                <small>Execution layer</small>
                                <strong>Admins</strong>
                            </div>
                            <div class="hero-proof-strip__item">
                                <small>Growth layer</small>
                                <strong>Managers</strong>
                            </div>
                        </div>

                        <article class="hero-video-card premium-card" data-reveal>
                            <div class="hero-video-card__visual">
                                <span class="hero-video-card__duration">{{ $content['hero']['video']['duration'] }}</span>
                                <button type="button" class="hero-video-card__play" aria-label="{{ $content['hero']['video']['cta'] }}" data-video-modal-open>
                                    <span></span>
                                </button>
                                <div class="hero-video-card__glow" aria-hidden="true"></div>
                            </div>
                            <div class="hero-video-card__copy">
                                <small>{{ $content['hero']['video']['label'] }}</small>
                                <strong>{{ $content['hero']['video']['title'] }}</strong>
                                <p>{{ $content['hero']['video']['text'] }}</p>
                                <button type="button" class="button button--ghost" data-video-modal-open>{{ $content['hero']['video']['cta'] }}</button>
                            </div>
                        </article>
                    </div>

                    <div class="hero-scene" data-reveal data-parallax-scene>
                        {{-- 3D effect: layered glow/orb background for dashboard stage depth. --}}
                        <div class="hero-scene__grid" aria-hidden="true"></div>
                        <div class="hero-scene__halo" aria-hidden="true"></div>
                        <div class="hero-scene__orb hero-scene__orb--one" data-depth="10" aria-hidden="true"></div>
                        <div class="hero-scene__orb hero-scene__orb--two" data-depth="-14" aria-hidden="true"></div>
                        <div class="hero-scene__plate hero-scene__plate--back" data-depth="-8" aria-hidden="true"></div>

                        {{-- 3D effect: main dashboard plane uses perspective tilt and floating support cards. --}}
                        <article class="dashboard-stage premium-card" data-tilt>
                            <div class="dashboard-stage__top">
                                <div class="window-dots" aria-hidden="true">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                                <div>
                                    <strong>{{ $brandName }} OS</strong>
                                    <small>Live operations</small>
                                </div>
                                <span class="live-pill">Live</span>
                            </div>

                            <div class="dashboard-metrics">
                                @foreach ($heroMetrics as [$label, $value, $delta, $tone])
                                    <div class="metric-tile metric-tile--{{ $tone }}">
                                        <small>{{ $label }}</small>
                                        <strong>{{ $value }}</strong>
                                        <span>{{ $delta }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="dashboard-layout">
                                <section class="dashboard-panel dashboard-panel--revenue">
                                    <div class="panel-head">
                                        <div>
                                            <span>Revenue chart</span>
                                            <strong>Oylik tushum</strong>
                                        </div>
                                        <small>So'nggi 6 oy</small>
                                    </div>
                                    <div class="bar-chart">
                                        @foreach ([38, 46, 54, 59, 72, 88] as $height)
                                            <span style="--bar-height: {{ $height }}%"></span>
                                        @endforeach
                                    </div>
                                </section>

                                <section class="dashboard-panel dashboard-panel--events">
                                    <div class="panel-head">
                                        <div>
                                            <span>Yaqin tadbirlar</span>
                                            <strong>CRM + Booking</strong>
                                        </div>
                                        <small>3 ta event</small>
                                    </div>
                                    <div class="event-list">
                                        @foreach ($events as [$hall, $date, $status])
                                            <div class="event-row">
                                                <div>
                                                    <strong>{{ $hall }}</strong>
                                                    <small>{{ $date }}</small>
                                                </div>
                                                <span>{{ $status }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </section>

                                <section class="dashboard-panel dashboard-panel--pipeline">
                                    <div class="panel-head">
                                        <div>
                                            <span>Bron pipeline</span>
                                            <strong>Realtime pipeline</strong>
                                        </div>
                                        <small>Bugun</small>
                                    </div>
                                    <div class="pipeline-list">
                                        @foreach ($pipeline as [$label, $count])
                                            <div class="pipeline-item">
                                                <span>{{ $label }}</span>
                                                <strong>{{ $count }}</strong>
                                            </div>
                                        @endforeach
                                    </div>
                                </section>
                            </div>
                        </article>

                        {{-- 3D effect: floating cards sit on separate z-layers for parallax depth. --}}
                        <article class="floating-card floating-card--revenue premium-card" data-depth="-10">
                            <small>Oylik tushum</small>
                            <strong>$48.7k</strong>
                            <span>+18.4% o'sish</span>
                        </article>

                        <article class="floating-card floating-card--occupancy premium-card" data-depth="12">
                            <small>Bandlik</small>
                            <strong>82%</strong>
                            <span>Peak kunlar ko'paymoqda</span>
                        </article>

                        <article class="floating-card floating-card--debt premium-card" data-depth="18">
                            <small>Qarzdorlik</small>
                            <strong>$4.2k</strong>
                            <span>-21% kamaydi</span>
                        </article>

                        <article class="floating-card floating-card--pipeline premium-card" data-depth="9">
                            <small>Pipeline</small>
                            <strong>61 lead</strong>
                            <span>Bugungi savdo oqimi</span>
                        </article>
                    </div>
                </div>
            </section>

            <section class="stats-strip">
                <div class="container stats-strip__grid">
                    @foreach ($content['stats'] as [$value, $label])
                        <article class="stat-card premium-card" data-reveal>
                            <small>{{ $label }}</small>
                            <strong>{{ $value }}</strong>
                            <span>Real biznes boshqaruvi uchun ishlab turgan platforma</span>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="section-block" id="problems">
                <div class="container">
                    <x-landing.section-heading eyebrow="Risk zonasi" :title="$content['problem']['title']" :subtitle="$content['problem']['subtitle']" align="center" />

                    <div class="problem-grid">
                        @foreach ($content['problem']['items'] as [$title, $text])
                            <article class="problem-card premium-card warning-card" data-reveal>
                                <span class="problem-card__icon" aria-hidden="true"></span>
                                <h3>{{ $title }}</h3>
                                <p>{{ $text }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="section-block section-block--muted">
                <div class="container transform-shell premium-card" data-reveal>
                    <div class="transform-copy">
                        <x-landing.section-heading eyebrow="Before / After" :title="$content['transformation']['title']" :subtitle="$content['transformation']['subtitle']" />

                        <div class="transform-list">
                            @foreach ($content['transformation']['items'] as [$before, $after])
                                <div class="transform-row">
                                    <div class="transform-state transform-state--before">
                                        <small>Before</small>
                                        <strong>{{ $before }}</strong>
                                    </div>
                                    <span class="transform-arrow" aria-hidden="true">-&gt;</span>
                                    <div class="transform-state transform-state--after">
                                        <small>After</small>
                                        <strong>{{ $after }}</strong>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="transform-visual" data-parallax-scene>
                        <div class="compare-pane compare-pane--chaos premium-card" data-depth="-8">
                            <span>Oldin</span>
                            <h3>Tartibsiz jarayon</h3>
                            <ul>
                                <li>Qo'lda yozilgan bronlar</li>
                                <li>Band sanalar chalkashadi</li>
                                <li>To'lovlar tarqalib ketadi</li>
                            </ul>
                        </div>
                        <div class="compare-pane compare-pane--control premium-card" data-depth="12">
                            <span>Keyin</span>
                            <h3>Nazorat ostidagi tizim</h3>
                            <ul>
                                <li>Avtomatik bron oqimi</li>
                                <li>Realtime bandlik</li>
                                <li>Moliyaviy ko'rinish</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section-block" id="demo">
                <div class="container showcase-grid">
                    <div class="showcase-copy" data-reveal>
                        <x-landing.section-heading eyebrow="Product showcase" :title="$content['solution']['title']" :subtitle="$content['solution']['subtitle']" />

                        <div class="showcase-list showcase-list--bento">
                            @foreach ($content['solution']['items'] as [$title, $text])
                                <article class="showcase-item showcase-item--{{ $loop->index === 0 ? 'wide' : ($loop->index === 3 ? 'accent' : 'default') }} premium-card">
                                    <h3>{{ $title }}</h3>
                                    <p>{{ $text }}</p>
                                </article>
                            @endforeach
                        </div>
                    </div>

                    <div class="product-mockup" data-reveal data-tilt>
                        <article class="product-screen premium-card">
                            <div class="product-screen__sidebar">
                                <span class="pill">Hall management</span>
                                <h3>Zallar boshqaruvi moduli</h3>
                                <p>Har bir venue bo'yicha bron, narx va kalendar nazorati.</p>

                                <div class="module-list">
                                    @foreach ($showcaseModules as $module)
                                        <div class="module-list__item">
                                            <strong>{{ $module['title'] }}</strong>
                                            <span>{{ $module['text'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="product-screen__board">
                                <div class="booking-board">
                                    <div class="panel-head">
                                        <div>
                                            <span>Har bir zal uchun kalendar</span>
                                            <strong>Booking board</strong>
                                        </div>
                                        <small>May 2026</small>
                                    </div>

                                    <div class="booking-board__rows">
                                        @foreach ($bookingBoard as [$hall, $date, $status, $state])
                                            <div class="booking-board__row">
                                                <div>
                                                    <strong>{{ $hall }}</strong>
                                                    <small>{{ $date }}</small>
                                                </div>
                                                <span class="state-pill state-pill--{{ $state }}">{{ $status }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="pricing-stack">
                                    <div class="pricing-stack__card premium-card">
                                        <small>Narx sozlash</small>
                                        <strong>Dinamik pricing</strong>
                                        <span>Peak kunlar uchun alohida tarif</span>
                                    </div>
                                    <div class="pricing-stack__card premium-card">
                                        <small>Band sanalar</small>
                                        <strong>Auto lock</strong>
                                        <span>Ikki martalik bronning oldi olinadi</span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section class="section-block section-block--panel">
                <div class="container">
                    <x-landing.section-heading eyebrow="Admin dashboard" title="Premium admin dashboard hissi" subtitle="Oddiy screenshot emas, real operatsion boshqaruv paneli kabi ishlaydigan HTML/CSS showcase." align="center" />

                    <div class="dashboard-showcase" data-reveal>
                        <aside class="dashboard-showcase__metrics">
                            @foreach ($heroMetrics as [$label, $value, $delta, $tone])
                                <article class="metric-card premium-card metric-card--{{ $tone }}">
                                    <small>{{ $label }}</small>
                                    <strong>{{ $value }}</strong>
                                    <span>{{ $delta }}</span>
                                </article>
                            @endforeach
                        </aside>

                        <div class="dashboard-showcase__main">
                            <article class="showcase-panel premium-card">
                                <div class="panel-head">
                                    <div>
                                        <span>Analytics charts</span>
                                        <strong>Oylik tushum chart</strong>
                                    </div>
                                    <small>Last 6 months</small>
                                </div>
                                <div class="line-chart">
                                    @foreach ([72, 64, 68, 48, 32, 18] as $point)
                                        <span style="--point-y: {{ $point }}%"></span>
                                    @endforeach
                                </div>
                            </article>

                            <div class="dashboard-showcase__bottom">
                                <article class="showcase-panel premium-card">
                                    <div class="panel-head">
                                        <div>
                                            <span>Yaqin tadbirlar</span>
                                            <strong>CRM + Booking</strong>
                                        </div>
                                        <small>Realtime</small>
                                    </div>
                                    <div class="event-list">
                                        @foreach ($events as [$hall, $date, $status])
                                            <div class="event-row">
                                                <div>
                                                    <strong>{{ $hall }}</strong>
                                                    <small>{{ $date }}</small>
                                                </div>
                                                <span>{{ $status }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </article>

                                <article class="showcase-panel premium-card">
                                    <div class="panel-head">
                                        <div>
                                            <span>Bron pipeline</span>
                                            <strong>Lead to payment</strong>
                                        </div>
                                        <small>Bugungi holat</small>
                                    </div>
                                    <div class="pipeline-list pipeline-list--compact">
                                        @foreach ($pipeline as [$label, $count])
                                            <div class="pipeline-item">
                                                <span>{{ $label }}</span>
                                                <strong>{{ $count }}</strong>
                                            </div>
                                        @endforeach
                                    </div>
                                </article>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section-block">
                <div class="container">
                    <x-landing.section-heading eyebrow="Benefits" :title="$content['benefits']['title']" :subtitle="$content['benefits']['subtitle']" align="center" />

                    <div class="benefit-grid">
                        @foreach ($content['benefits']['items'] as [$value, $title, $description])
                            <x-landing.benefit-card :value="$value" :title="$title" :description="$description" data-reveal />
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="section-block section-block--muted">
                <div class="container">
                    <x-landing.section-heading eyebrow="Kimlar uchun" :title="$content['audience']['title']" :subtitle="$content['audience']['subtitle']" align="center" />

                    <div class="audience-grid">
                        @foreach ($content['audience']['items'] as [$title, $text, $items])
                            <article class="audience-card premium-card" data-reveal>
                                <span class="pill">Role based value</span>
                                <h3>{{ $title }}</h3>
                                <p>{{ $text }}</p>
                                <ul class="audience-list">
                                    @foreach ($items as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                                <a href="{{ $registerUrl }}" class="button button--ghost">Registratsiyani boshlash</a>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="section-block section-block--muted" id="features">
                <div class="container">
                    <x-landing.section-heading eyebrow="Platforma" :title="$content['features']['title']" :subtitle="$content['features']['subtitle']" align="center" />

                    <div class="feature-grid">
                        @foreach ($content['features']['items'] as [$icon, $title, $description, $tone])
                            <x-landing.feature-card :icon="$iconSvgs[$icon] ?? $iconSvgs['layout']" :title="$title" :description="$description" :tone="$tone" data-reveal />
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="section-block">
                <div class="container">
                    <div class="urgency-banner premium-card" data-reveal>
                        <div>
                            <span class="pill">Revenue urgency</span>
                            <h2>{{ $content['urgency']['title'] }}</h2>
                            <p>{{ $content['urgency']['text'] }}</p>
                        </div>
                        <div class="urgency-banner__action">
                            <strong>{{ $content['urgency']['highlight'] }}</strong>
                            <a href="#contact" class="button button--primary">{{ $content['hero']['primary'] }}</a>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section-block">
                <div class="container">
                    <x-landing.section-heading eyebrow="Social proof" :title="$content['testimonials']['title']" subtitle="Tizim foydasini operatsion jamoalar real ish jarayonida his qilmoqda." align="center" />

                    <div class="testimonial-grid">
                        @foreach ($content['testimonials']['items'] as [$quote, $author, $role])
                            <x-landing.testimonial-card :quote="$quote" :author="$author" :role="$role" data-reveal />
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="section-block section-block--panel" id="pricing">
                <div class="container">
                    <x-landing.section-heading eyebrow="Pricing" :title="$content['pricing']['title']" :subtitle="$content['pricing']['subtitle']" align="center" />

                    <div class="pricing-grid">
                        @foreach ($pricingPlans as [$name, $price, $period, $description, $features, $highlighted])
                            <x-landing.pricing-card
                                :name="$name"
                                :price="$price"
                                :period="$period"
                                :description="$description"
                                :features="$features"
                                :highlighted="$highlighted"
                                :badge="$highlighted ? $content['pricing']['popular'] : null"
                                cta-text="Bepul demo olish"
                                data-reveal
                            />
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="section-block section-block--muted">
                <div class="container">
                    <div class="calculator-shell premium-card" data-reveal data-pricing-calculator data-plans='@json($calculatorPlans)'>
                        <div class="calculator-copy">
                            <x-landing.section-heading eyebrow="Pricing calculator" title="Qaysi tarif sizga mosligini tez hisoblang" subtitle="Zallar soni va oylik bron oqimiga qarab tavsiya etilgan plan, taxminiy oylik xarajat va qaytariladigan daromad ko'rinadi." />
                        </div>

                        <div class="calculator-grid">
                            <article class="calculator-panel premium-card">
                                <label class="calculator-field">
                                    <span>Zallar soni</span>
                                    <input type="range" min="1" max="6" value="2" data-calc-halls>
                                    <strong data-calc-halls-output>2 ta zal</strong>
                                </label>

                                <label class="calculator-field">
                                    <span>Oylik bron oqimi</span>
                                    <input type="range" min="20" max="180" step="10" value="70" data-calc-leads>
                                    <strong data-calc-leads-output>70 ta lead / oy</strong>
                                </label>

                                <label class="calculator-field">
                                    <span>Tanlangan plan</span>
                                    <select data-calc-plan>
                                        @foreach ($calculatorPlans as $plan)
                                            <option value="{{ $plan['name'] }}" @selected($plan['highlighted'])>{{ $plan['name'] }}</option>
                                        @endforeach
                                    </select>
                                </label>
                            </article>

                            <article class="calculator-summary premium-card">
                                <div class="calculator-summary__top">
                                    <small>Tavsiya</small>
                                    <strong data-calc-recommendation>Pro</strong>
                                </div>
                                <div class="calculator-metrics">
                                    <div>
                                        <span>Oylik abonent to'lovi</span>
                                        <strong data-calc-price>990 000 UZS</strong>
                                    </div>
                                    <div>
                                        <span>Yillik ekvivalenti</span>
                                        <strong data-calc-yearly>11 880 000 UZS</strong>
                                    </div>
                                    <div>
                                        <span>Qaytariladigan potensial bron</span>
                                        <strong data-calc-recovered>21 lead</strong>
                                    </div>
                                </div>
                                <p data-calc-note>Ko'proq zal va yuqori bron oqimi uchun chuqur analytics va multi-hall nazorat tavsiya qilinadi.</p>
                                <div class="calculator-actions">
                                    <a href="{{ $registerUrl }}" class="button button--primary">Registratsiya</a>
                                    <a href="#contact" class="button button--ghost">Demo olish</a>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section-block">
                <div class="container">
                    <div class="funnel-shell premium-card" data-reveal data-demo-funnel>
                        <div class="funnel-copy">
                            <x-landing.section-heading eyebrow="Demo booking funnel" title="3 qadamda demo yoki registratsiya yo'lini tanlang" subtitle="Uzun form o'rniga, kerakli yo'nalishni tanlaysiz va tizim sizni to'g'ri CTA'ga olib boradi." />
                        </div>

                        <div class="funnel-grid">
                            <div class="funnel-steps">
                                @foreach ($demoFunnelSteps as $stepIndex => $step)
                                    <article class="funnel-step premium-card @if($stepIndex === 0) is-active @endif" data-funnel-step="{{ $stepIndex }}">
                                        <small>{{ $step['eyebrow'] }}</small>
                                        <h3>{{ $step['title'] }}</h3>
                                        <p>{{ $step['text'] }}</p>
                                        <div class="funnel-choices">
                                            @foreach ($step['choices'] as $choice)
                                                <button
                                                    type="button"
                                                    class="funnel-choice @if($loop->first) is-selected @endif"
                                                    data-funnel-choice
                                                    data-step="{{ $stepIndex }}"
                                                    data-value="{{ $choice['value'] }}"
                                                >
                                                    {{ $choice['label'] }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </article>
                                @endforeach
                            </div>

                            <aside class="funnel-summary premium-card">
                                <small>Personalized route</small>
                                <h3 data-funnel-title>Pro demo tavsiya qilinadi</h3>
                                <p data-funnel-text>Jamoangiz uchun mos demo oqimi tanlandi. Endi tez onboarding yoki to'g'ridan-to'g'ri registratsiyaga o'tishingiz mumkin.</p>
                                <div class="funnel-pills">
                                    <span data-funnel-role-pill>Owner flow</span>
                                    <span data-funnel-scale-pill>Growth setup</span>
                                    <span data-funnel-timing-pill>Shu hafta</span>
                                </div>
                                <div class="funnel-summary__actions">
                                    <a href="#contact" class="button button--primary" data-funnel-primary>Demo bron qilish</a>
                                    <a href="{{ $registerUrl }}" class="button button--ghost" data-funnel-secondary>Registratsiya</a>
                                </div>
                            </aside>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section-block">
                <div class="container">
                    <div class="objection-shell premium-card" data-reveal>
                        <div class="objection-shell__copy">
                            <x-landing.section-heading eyebrow="Trust and objections" :title="$content['objections']['title']" :subtitle="$content['objections']['subtitle']" />
                            <div class="objection-trust">
                                @foreach ($content['objections']['trust'] as $item)
                                    <span>{{ $item }}</span>
                                @endforeach
                            </div>
                        </div>

                        <div class="objection-grid">
                            @foreach ($content['objections']['items'] as [$title, $text])
                                <article class="objection-card premium-card">
                                    <h3>{{ $title }}</h3>
                                    <p>{{ $text }}</p>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <section class="section-block section-block--cta" id="final-cta">
                <div class="container">
                    <div class="final-cta premium-card" data-reveal>
                        <div class="final-cta__copy">
                            <span class="pill">{{ $brandName }}</span>
                            <h2>{{ $content['final']['title'] }}</h2>
                            <p>{{ $content['final']['text'] }}</p>
                            <div class="final-cta__points">
                                @foreach ($content['final']['points'] as $point)
                                    <span>{{ $point }}</span>
                                @endforeach
                            </div>
                        </div>

                        <div class="final-cta__actions">
                            <a href="#contact" class="button button--primary">{{ $content['final']['buttons'][0] }}</a>
                            <a href="#contact" class="button button--secondary">{{ $content['final']['buttons'][1] }}</a>
                            <a href="#contact" class="button button--ghost">{{ $content['final']['buttons'][2] }}</a>
                            <a href="{{ $registerUrl }}" class="button button--primary button--soft">{{ $content['final']['buttons'][3] }}</a>
                            <a href="#contact" class="button button--ghost">{{ $content['final']['buttons'][4] }}</a>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section-block section-block--tight" id="contact">
                <div class="container contact-grid-wrap">
                    <div data-reveal>
                        <x-landing.section-heading eyebrow="Contact" :title="$content['contact']['title']" :subtitle="$content['contact']['text']" />
                    </div>

                    <div class="contact-grid">
                        @foreach ($content['contact']['items'] as [$label, $value, $href])
                            <article class="contact-card premium-card" data-reveal>
                                <small>{{ $label }}</small>
                                <strong><a href="{{ $href }}" @if(str_starts_with($href, 'http')) target="_blank" rel="noopener noreferrer" @endif>{{ $value }}</a></strong>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        </main>

        <footer class="site-footer">
            <div class="container footer-grid">
                <div class="footer-brand">
                    <a href="{{ $landingUrl }}" class="brand brand--footer">
                        <span class="brand__mark">
                            @if($brandLogo?->url())
                                <img src="{{ $brandLogo->url() }}" alt="{{ $brandName }}">
                            @else
                                <strong>{{ strtoupper(substr($brandName, 0, 2)) }}</strong>
                            @endif
                        </span>
                        <span class="brand__copy">
                            <strong>{{ $brandName }}</strong>
                            <small>{{ $content['tagline'] }}</small>
                        </span>
                    </a>
                    <p>{{ $content['meta_description'] }}</p>
                </div>

                <div class="footer-links">
                    <a href="#features">Imkoniyatlar</a>
                    <a href="#demo">Demo</a>
                    <a href="#pricing">Narxlar</a>
                    <a href="#contact">Bog'lanish</a>
                    <a href="{{ $loginUrl }}">Kirish</a>
                    <a href="{{ $registerUrl }}">Ro'yxatdan o'tish</a>
                </div>
            </div>

            <div class="container footer-bottom">
                <span>{{ $brandName }} 2026 - Barcha huquqlar himoyalangan.</span>
            </div>
        </footer>

        <div class="mobile-cta-bar" aria-label="Mobile quick actions">
            <a href="#contact" class="button button--secondary">Demo</a>
            <a href="{{ $registerUrl }}" class="button button--primary">Registratsiya</a>
        </div>

        <div class="video-modal" data-video-modal hidden aria-hidden="true">
            <div class="video-modal__backdrop" data-video-modal-close></div>
            <div class="video-modal__dialog premium-card" role="dialog" aria-modal="true" aria-labelledby="demo-video-title">
                <button type="button" class="video-modal__close" aria-label="Close demo video" data-video-modal-close>&times;</button>

                @if ($content['hero']['video']['embed'])
                    <div class="video-modal__frame">
                        <iframe
                            data-video-embed
                            title="{{ $content['hero']['video']['title'] }}"
                            src=""
                            data-src="{{ $content['hero']['video']['embed'] }}"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                        ></iframe>
                    </div>
                @else
                    <div class="video-modal__fallback">
                        <div class="video-modal__fallback-copy">
                            <small>Interactive walkthrough</small>
                            <h3 id="demo-video-title">{{ $content['hero']['video']['title'] }}</h3>
                            <p>{{ $content['hero']['video']['text'] }}</p>
                        </div>
                        <div class="video-modal__timeline">
                            <article class="video-timeline-card premium-card">
                                <small>00:08</small>
                                <strong>Bron va kalendar oqimi</strong>
                                <p>Band kunlar, zal availability va auto lock jarayoni ko'rsatiladi.</p>
                            </article>
                            <article class="video-timeline-card premium-card">
                                <small>00:16</small>
                                <strong>CRM + pipeline nazorati</strong>
                                <p>Lead'dan to'lovgacha bo'lgan jarayon realtime panelda ochiladi.</p>
                            </article>
                            <article class="video-timeline-card premium-card">
                                <small>00:30</small>
                                <strong>Rahbar dashboard</strong>
                                <p>Tushum, qarzdorlik va bandlik ko'rsatkichlari bir sahifada yakunlanadi.</p>
                            </article>
                        </div>
                        <div class="video-modal__fallback-actions">
                            <a href="#contact" class="button button--primary">Live demo so'rash</a>
                            <a href="{{ $registerUrl }}" class="button button--ghost">Registratsiya</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- 3D: hero dashboard stage, floating KPI cards, pseudo-3D product showcase, layered compare panels. --}}
        {{-- Animation: sticky glass header, scroll reveal, desktop parallax layers, card tilt, hover lift states, mobile sticky CTA. --}}
    </div>
</x-layouts.landing>
