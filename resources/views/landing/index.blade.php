@php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use App\Models\LandingContent;
use App\Models\SubscriptionPlan;

$locale = in_array(request('lang', 'uz'), ['uz', 'uzc', 'ru', 'en'], true) ? request('lang', 'uz') : 'uz';
$resolvedSetting = Schema::hasTable('settings') ? \App\Models\Setting::global() : null;
$resolvedMediaAssets = Schema::hasTable('media_assets') ? \App\Models\MediaAsset::keyed() : collect();
$brandName = $resolvedSetting?->restaurant_name ?: 'MyRestoran';
$fallbackLocale = 'uz';
$fallbackPack = $languageLines->get($fallbackLocale, collect());
$langPack = $languageLines->get($locale, collect());
$langText = static fn (string $key, string $default) => filled($langPack->get($key))
    ? $langPack->get($key)
    : (filled($fallbackPack->get($key)) ? $fallbackPack->get($key) : $default);
$loginUrl = Route::has('login') ? route('login', ['lang' => $locale]) : '#';
$registerUrl = Route::has('register') ? route('register', ['lang' => $locale]) : '#';
$formatMoney = static function (float|int|string $amount, string $currency): string {
    $amount = (float) $amount;

    return match (strtoupper($currency)) {
        'USD' => '$'.number_format($amount, 0, '.', ','),
        'EUR' => '€'.number_format($amount, 0, '.', ','),
        default => number_format($amount, 0, '.', ' ').' '.strtoupper($currency),
    };
};
$formatPlanPeriod = static function (?string $billingCycle, ?int $durationDays) use ($locale): string {
    $days = max((int) $durationDays, 1);

    return match ($billingCycle) {
        'yearly' => $locale === 'en' ? '/year' : ($locale === 'ru' ? '/год' : '/yil'),
        'quarterly' => $locale === 'en' ? '/quarter' : ($locale === 'ru' ? '/kvartal' : '/chorak'),
        'manual' => $locale === 'en' ? "/{$days} days" : ($locale === 'ru' ? "/{$days} дней" : "/{$days} kun"),
        default => $locale === 'en' ? '/month' : ($locale === 'ru' ? '/месяц' : '/oy'),
    };
};
$iconSvgs = [
    'calendar' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 2V5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/><path d="M16 2V5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/><path d="M3.5 9.09H20.5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/><rect x="3" y="4.5" width="18" height="16.5" rx="3" stroke="currentColor" stroke-width="1.75"/><path d="M8 13H8.01" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/><path d="M12 13H12.01" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/><path d="M16 13H16.01" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>',
    'users' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16 21V19C16 17.3431 14.6569 16 13 16H7C5.34315 16 4 17.3431 4 19V21" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/><circle cx="10" cy="8" r="4" stroke="currentColor" stroke-width="1.75"/><path d="M20 21V19C20 17.5311 18.9429 16.3092 17.55 16.054" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/><path d="M14.55 4.05402C15.9429 4.30921 17 5.53114 17 7C17 8.46886 15.9429 9.69079 14.55 9.94598" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/></svg>',
    'wallet' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 7.5C3 5.84315 4.34315 4.5 6 4.5H18C19.6569 4.5 21 5.84315 21 7.5V16.5C21 18.1569 19.6569 19.5 18 19.5H6C4.34315 19.5 3 18.1569 3 16.5V7.5Z" stroke="currentColor" stroke-width="1.75"/><path d="M17 12H17.01" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/><path d="M3.5 8.5H20.5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/></svg>',
    'chart' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 19.5H20" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/><path d="M7 16L10.5 12.5L13.5 14.5L18 9" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 9H18V12" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    'package' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3L19 7L12 11L5 7L12 3Z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/><path d="M19 7V17L12 21L5 17V7" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/><path d="M12 11V21" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/></svg>',
    'send' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M21.5 3.5L10.5 14.5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/><path d="M21.5 3.5L14.5 20.5L10.5 14.5L3.5 10.5L21.5 3.5Z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/></svg>',
    'layout' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="3" stroke="currentColor" stroke-width="1.75"/><path d="M9 4V20" stroke="currentColor" stroke-width="1.75"/><path d="M9 10H21" stroke="currentColor" stroke-width="1.75"/></svg>',
];

$txt = [
    'uz' => [
        'meta_title' => "MyRestoran | To'yxona boshqaruv SaaS",
        'meta_description' => "To'yxona va banket zallari uchun bron, CRM, moliya, ombor va analitika platformasi.",
        'brand' => 'MyRestoran',
        'tagline' => "To'yxona biznesi uchun premium boshqaruv platformasi",
        'nav' => [
            'product' => 'Imkoniyatlar',
            'benefits' => 'Demo',
            'pricing' => 'Narxlar',
            'contact' => "Bog'lanish",
            'login' => 'Kirish',
            'register' => "Ro'yxatdan o'tish",
        ],
            'hero' => [
                'badge' => 'Wedding hall management SaaS',
                'title' => "To'yxonangizni raqamlashtiring va daromadingizni 2x oshiring",
            'text' => "Bronlar, mijozlar va moliyani bitta tizimda boshqaring — hech qanday chalkashliksiz.",
            'micro' => "Har bir bo'sh kun — yo'qotilgan pul",
            'primary' => "Bepul demo olish",
            'secondary' => "7 kun bepul sinov",
            'tertiary' => 'Tizimni ulash',
        ],
        'stats' => [
            ['120+', 'Faol zallar'],
            ['18k', 'Oylik bron'],
            ['99.9%', "To'lov nazorati"],
        ],
        'problem_title' => 'Muammolar sizga tanishmi?',
        'problems' => [
            ['Bronlar chalkashib ketadi', 'Bir sana uchun bir nechta buyurtma yozilib ketadi.'],
            ['Band kunlarni topish qiyin', "Qaysi kunlar bandligini tez ko'rish qiyinlashadi."],
            ["Pul oqimi ko'rinmaydi", "Daromad va qarzdorlik qayerga ketayotganini bilmaysiz."],
            ['Adminlar xato qiladi', "Qo'lda boshqaruv operatsion xatolarni ko'paytiradi."],
        ],
        'problem_note' => 'Bu muammolar sizga oyiga millionlab zarar keltirishi mumkin',
        'solution_title' => 'Zallar boshqaruvi moduli',
        'solution_text' => 'Har bir zal uchun kalendar, avtomatik bron boshqaruvi, band sanalar va narx sozlash imkoniyati.',
        'solutions' => [
            ['Har bir zal uchun kalendar', "Kun, hafta va oy ko'rinishida nazorat."],
            ['Bronlarni avtomatik boshqarish', "So'rov, tasdiq va qayta bronlar tizimda yuradi."],
            ['Band sanalarni ko\'rish', "Bo'sh va band kunlar bir qarashda."],
            ['Narxlarni sozlash', 'Har bir zal va sana uchun mos narx siyosati.'],
        ],
        'preview' => [
            'title' => 'Rahbarlar uchun yaratilgan boshqaruv paneli',
            'text' => "KPI, bron jadvali, moliya va jamoa vazifalari bitta dark SaaS interfeysda ko'rinadi.",
            'revenue' => 'Oylik tushum',
            'occupancy' => 'Bandlik',
            'debt' => 'Qarzdorlik',
            'table' => 'Yaqin tadbirlar',
            'pipeline' => 'Bron pipeline',
        ],
        'transform_title' => "MyRestoran bilan hammasi o'zgaradi",
        'transforms' => [
            ["Tartibsiz bronlar", "To'liq avtomatlashtirilgan tizim"],
            ["Qo'lda hisob", 'Real vaqt analytics'],
            ["Yo'qotilgan mijozlar", 'Har bir mijoz nazoratda'],
        ],
        'benefits_title' => 'Tizim siz uchun nima qiladi?',
        'benefits' => [
            ['+30%', "Ko'proq bronlar", "Bo'sh sanalarni tez to'ldiring va so'rovlarni yo'qotmang."],
            ['0 xato', 'Xatolarsiz kalendar', 'Band sanalar va bronlar yagona tizimda sinxron yuradi.'],
            ['24/7', 'Daromad nazorati', "To'lovlar va foyda har doim ko'rinib turadi."],
        ],
        'features_title' => 'Platforma imkoniyatlari',
        'features' => [
            ['calendar', 'Bron tizimi', 'Band sanalarni va bronlar oqimini markazdan boshqaring.', 'primary'],
            ['users', 'CRM', "Mijozlar tarixini, statusini va aloqa jarayonini ko'ring.", 'primary'],
            ['wallet', "Moliyaviy nazorat", "Daromad, avans va qarzdorlikni nazorat qiling.", 'finance'],
            ['package', 'Ombor', "Mahsulot va sarf-harajatlarni tizimli boshqaring.", 'activity'],
            ['chart', 'Analytics', 'Qarorlarni data va trendlar asosida qabul qiling.', 'analytics'],
            ['layout', 'Admin panel', 'Hammasini bitta boshqaruv markazidan boshqaring.', 'primary'],
            ['send', 'Telegram integratsiya', "Muhim jarayonlar bo'yicha tezkor xabarnomalar oling.", 'activity'],
        ],
        'money_title' => "Har bir bo'sh kun - bu yo'qotilgan daromad",
        'money_text' => "MyRestoran yordamida siz yo'qotilayotgan bronlarni qaytarasiz va daromadingizni oshirasiz.",
        'money_highlight' => "+30% ko'proq bronlar",
        'testimonials_title' => 'Bizga ishonayotganlar',
        'testimonials' => [
            ['Bu tizim bilan bronlarni boshqarish 3x osonlashdi.', 'Javohir', 'Wedding Hall egasi'],
            ["Endi xatolar yo'q, hammasi tizimli.", 'Nilufar A.', 'Administrator'],
            ["Mijozlar bilan ishlash tezlashdi, jamoani nazorat qilish ham ancha qulaylashdi.", 'Sardor M.', 'Boshqaruvchi'],
        ],
        'pricing_title' => 'Oddiy va tushunarli narxlar',
        'popular' => 'Eng ommabop',
        'plans' => [
            ['Basic', '$20', '/oy', '1 ta zal va 1 admin uchun.', ['1 ta zal', '1 admin', 'Asosiy bron taqvimi'], false],
            ['Standard', '$49', '/oy', '3 ta zal va analytics bilan.', ['3 ta zal', 'Analytics', "To'lov nazorati", 'Telegram integratsiya'], true],
            ['Premium', '$99', '/oy', 'Unlimited + AI analytics.', ['Cheksiz zallar', 'AI analytics', 'Advanced hisobotlar'], false],
        ],
        'final_title' => 'Tizimni ulashni boshlang',
        'final_text' => "To'yxonangizni boshqarishni professional darajaga olib chiqing. Bugunoq boshlang - ertaga kech bo'lishi mumkin.",
        'contact_title' => "Jamoamiz bilan bog'laning",
        'contact_text' => 'Demo yoki savollar uchun tezda chiqamiz.',
        'rights' => 'Barcha huquqlar himoyalangan.',
        'demo_request' => "Demo so'rash",
    ],
    'uzc' => [
        'meta_title' => 'MyRestoran | Тўйхона бошқарув SaaS',
        'meta_description' => 'Тўйхона ва банкет заллари учун брон, CRM, молия, омбор ва аналитика платформаси.',
        'brand' => 'MyRestoran',
        'tagline' => 'Тўйхона бизнеси учун премиум бошқарув платформаси',
        'nav' => [
            'product' => 'Имкониятлар',
            'benefits' => 'Демо',
            'pricing' => 'Нархлар',
            'contact' => 'Боғланиш',
            'login' => 'Кириш',
            'register' => 'Рўйхатдан ўтиш',
        ],
        'hero' => [
            'badge' => 'Wedding hall management SaaS',
            'title' => 'Тўйхонангизни рақамлаштиринг ва даромадингизни 2x оширинг',
            'text' => 'Бронлар, мижозлар ва молияни битта тизимда бошқаринг — ҳеч қандай чалкашликсиз.',
            'micro' => 'Ҳар бир бўш кун — йўқотилган пул',
            'primary' => 'Бепул демо олиш',
            'secondary' => '7 кун бепул синов',
            'tertiary' => 'Тизимни улаш',
        ],
        'stats' => [
            ['120+', 'Фаол заллар'],
            ['18k', 'Ойлик брон'],
            ['99.9%', 'Тўлов назорати'],
        ],
        'problem_title' => 'Муаммолар сизга танишми?',
        'problems' => [
            ['Бронлар чалкашиб кетади', 'Бир сана учун бир нечта буюртма ёзилиб кетади.'],
            ['Банд кунларни топиш қийин', 'Қайси кунлар бандлигини тез кўриш қийинлашади.'],
            ['Пул оқими кўринмайди', 'Даромад ва қарздорлик қаерга кетаётганини билмайсиз.'],
            ['Админлар хато қилади', 'Қўлда бошқарув операцион хатоларни кўпайтиради.'],
        ],
        'problem_note' => 'Бу муаммолар сизга ойига миллионлаб зарар келтириши мумкин',
        'solution_title' => 'Заллар бошқаруви модули',
        'solution_text' => 'Ҳар бир зал учун календар, автоматик брон бошқаруви, банд саналар ва нарх созлаш имконияти.',
        'solutions' => [
            ['Ҳар бир зал учун календар', 'Кун, ҳафта ва ой кўринишида назорат.'],
            ['Бронларни автоматик бошқариш', 'Сўров, тасдиқ ва қайта бронлар тизимда юради.'],
            ['Банд саналарни кўриш', 'Бўш ва банд кунлар бир қарашда.'],
            ['Нархларни созлаш', 'Ҳар бир зал ва сана учун мос нарх сиёсати.'],
        ],
        'preview' => [
            'title' => 'Раҳбарлар учун яратилган бошқарув панели',
            'text' => 'KPI, брон жадвали, молия ва жамоа вазифалари битта dark SaaS интерфейсида кўринади.',
            'revenue' => 'Ойлик тушум',
            'occupancy' => 'Бандлик',
            'debt' => 'Қарздорлик',
            'table' => 'Яқин тадбирлар',
            'pipeline' => 'Брон pipeline',
        ],
        'transform_title' => 'MyRestoran билан ҳаммаси ўзгаради',
        'transforms' => [
            ['Тартибсиз бронлар', 'Тўлиқ автоматлаштирилган тизим'],
            ['Қўлда ҳисоб', 'Real time analytics'],
            ['Йўқотилган мижозлар', 'Ҳар бир мижоз назоратда'],
        ],
        'benefits_title' => 'Тизим сиз учун нима қилади?',
        'benefits' => [
            ['+30%', 'Кўпроқ бронлар', 'Бўш саналарни тез тўлдиринг ва сўровларни йўқотманг.'],
            ['0 хато', 'Хатоларсиз календар', 'Банд саналар ва бронлар ягона тизимда синхрон юради.'],
            ['24/7', 'Даромад назорати', 'Тўловлар ва фойда ҳар доим кўриниб туради.'],
        ],
        'features_title' => 'Платформа имкониятлари',
        'features' => [
            ['calendar', 'Брон тизими', 'Банд саналарни ва бронлар оқимини марказдан бошқаринг.', 'primary'],
            ['users', 'CRM', 'Мижозлар тарихини, статусини ва алоқа жараёнини кўринг.', 'primary'],
            ['wallet', 'Молиявий назорат', 'Даромад, аванс ва қарздорликни назорат қилинг.', 'finance'],
            ['package', 'Омбор', 'Маҳсулот ва сарф-харажатларни тизимли бошқаринг.', 'activity'],
            ['chart', 'Analytics', 'Қарорларни data ва trendлар асосида қабул қилинг.', 'analytics'],
            ['layout', 'Админ панел', 'Ҳаммасини битта бошқарув марказидан бошқаринг.', 'primary'],
            ['send', 'Telegram интеграция', 'Муҳим жараёнлар бўйича тезкор хабарномалар олинг.', 'activity'],
        ],
        'money_title' => 'Ҳар бир бўш кун - бу йўқотилган даромад',
        'money_text' => 'MyRestoran ёрдамида сиз йўқотилаётган бронларни қайтарасиз ва даромадингизни оширасиз.',
        'money_highlight' => '+30% кўпроқ бронлар',
        'testimonials_title' => 'Бизга ишонаётганлар',
        'testimonials' => [
            ['Бу тизим билан бронларни бошқариш 3x осонлашди.', 'Жавоҳир', 'Wedding Hall эгаси'],
            ['Энди хатолар йўқ, ҳаммаси тизимли.', 'Нилуфар А.', 'Администратор'],
            ['Мижозлар билан ишлаш тезлашди, жамоани назорат қилиш ҳам анча қулайлашди.', 'Сардор М.', 'Бошқарувчи'],
        ],
        'pricing_title' => 'Оддий ва тушунарли нархлар',
        'popular' => 'Энг оммабоп',
        'plans' => [
            ['Basic', '$20', '/ой', '1 та зал ва 1 админ учун.', ['1 та зал', '1 админ', 'Асосий брон тақвими'], false],
            ['Standard', '$49', '/ой', '3 та зал ва analytics билан.', ['3 та зал', 'Analytics', 'Тўлов назорати', 'Telegram интеграция'], true],
            ['Premium', '$99', '/ой', 'Unlimited + AI analytics.', ['Чексиз заллар', 'AI analytics', 'Advanced ҳисоботлар'], false],
        ],
        'final_title' => 'Тизимни улашни бошланг',
        'final_text' => 'Тўйхонангизни бошқаришни профессионал даражага олиб чиқинг. Бугуноқ бошланг - эртага кеч бўлиши мумкин.',
        'contact_title' => 'Жамоамиз билан боғланинг',
        'contact_text' => 'Демо ёки саволлар учун тезда чиқамиз.',
        'rights' => 'Барча ҳуқуқлар ҳимояланган.',
        'demo_request' => 'Демо сўраш',
    ],
    'ru' => [
        'meta_title' => 'MyRestoran | SaaS для управления банкетным залом',
        'meta_description' => 'Платформа для броней, CRM, финансов, склада и аналитики банкетных залов.',
        'brand' => 'MyRestoran',
        'tagline' => 'Премиальная платформа управления для банкетного бизнеса',
        'nav' => [
            'product' => 'Продукт',
            'benefits' => 'Результат',
            'pricing' => 'Тарифы',
            'contact' => 'Контакты',
            'login' => 'Вход',
            'register' => 'Регистрация',
        ],
        'hero' => [
            'badge' => 'Wedding hall management SaaS',
            'title' => 'Оцифруйте зал и удвойте выручку',
            'text' => 'Управляйте залами, бронями, клиентами и финансами в одной платформе.',
            'primary' => 'Смотреть демо',
            'secondary' => 'Попробовать бесплатно',
            'tertiary' => 'Подключить систему',
        ],
        'stats' => [
            ['120+', 'Активных залов'],
            ['18k', 'Броней в месяц'],
            ['99.9%', 'Контроль оплат'],
        ],
        'problem_title' => 'Ручное управление замедляет рост выручки',
        'problems' => [
            ['Путаница в бронях', 'На одну дату могут попасть несколько заказов.'],
            ['Потеря выручки', 'Авансы и долги не видны в одном месте.'],
            ['Слишком много ручной работы', 'Excel и чаты забирают время команды.'],
            ['Слабый контроль', 'Трудно понять, какой зал и менеджер дают прибыль.'],
        ],
        'solution_title' => 'Модуль управления залами',
        'solution_text' => 'Календарь по каждому залу, автоматизация броней, занятые даты и гибкая настройка цен.',
        'solutions' => [
            ['Календарь по каждому залу', 'Контроль по дням, неделям и месяцам.'],
            ['Автоматизация броней', 'Заявки, подтверждения и переносы внутри одной системы.'],
            ['Видимость занятых дат', 'Свободные и занятые дни видны сразу.'],
            ['Настройка цен', 'Гибкие цены по залу и по дате.'],
        ],
        'preview' => [
            'title' => 'Панель управления для владельцев и админов',
            'text' => 'KPI, график броней, финансы и задачи команды в одном dark SaaS интерфейсе.',
            'revenue' => 'Выручка за месяц',
            'occupancy' => 'Загрузка',
            'debt' => 'Задолженность',
            'table' => 'Ближайшие события',
            'pipeline' => 'Воронка броней',
        ],
        'benefits_title' => 'Почему MyRestoran?',
        'benefits' => [
            ['+30%', 'Больше броней', 'Быстрее заполняйте свободные даты.'],
            ['0 ошибок', 'Чистый календарь', 'Даты и статусы броней всегда синхронны.'],
            ['24/7', 'Контроль выручки', 'Платежи и прибыль видны в любой момент.'],
        ],
        'features_title' => 'Возможности платформы',
        'features' => [
            ['calendar', 'Система бронирования', 'Управление датами и потоком событий.'],
            ['users', 'CRM - база клиентов', 'Карточки клиентов и история общения.'],
            ['wallet', 'Контроль оплат', 'Авансы, долги и движение кассы.'],
            ['chart', 'Аналитика и отчёты', 'Загрузка, выручка и результаты команды.'],
            ['layout', 'Админ панель', 'Единый центр управления для руководителя.'],
            ['send', 'Интеграция с Telegram', 'Быстрые уведомления и операционные сигналы.'],
        ],
        'testimonials_title' => 'Что говорят владельцы залов',
        'testimonials' => [
            ['С этой системой управлять бронями стало в 3 раза проще.', 'Жавохир', 'Владелец Wedding Hall'],
            ['Больше нет ошибок, всё стало системно.', 'Нилуфар А.', 'Администратор'],
            ['Работа с клиентами ускорилась, а контроль команды стал заметно удобнее.', 'Сардор М.', 'Управляющий'],
        ],
        'pricing_title' => 'SaaS тарифы для банкетного бизнеса',
        'popular' => 'Самый популярный',
        'plans' => [
            ['Basic', '$20', '/мес', 'Для 1 зала и 1 администратора.', ['1 зал', '1 админ', 'Базовый календарь броней'], false],
            ['Standard', '$49', '/мес', '3 зала и analytics.', ['3 зала', 'Analytics', 'Контроль оплат', 'Telegram интеграция'], true],
            ['Premium', '$99', '/мес', 'Unlimited + AI analytics.', ['Безлимитные залы', 'AI analytics', 'Расширенные отчёты'], false],
        ],
        'final_title' => 'Начните подключение системы',
        'final_text' => 'Выведите управление банкетным залом на профессиональный уровень и автоматизируйте ключевые процессы.',
        'contact_title' => 'Свяжитесь с нашей командой',
        'contact_text' => 'Быстро ответим по демо и внедрению.',
        'rights' => 'Все права защищены.',
        'demo_request' => 'Запросить демо',
    ],
    'en' => [
        'meta_title' => 'MyRestoran | Wedding hall management SaaS',
        'meta_description' => 'Bookings, CRM, finance, inventory and analytics for wedding halls in one SaaS platform.',
        'brand' => 'MyRestoran',
        'tagline' => 'Premium operations platform for wedding hall businesses',
        'nav' => [
            'product' => 'Features',
            'benefits' => 'Demo',
            'pricing' => 'Pricing',
            'contact' => 'Contact',
            'login' => 'Login',
            'register' => 'Register',
        ],
            'hero' => [
                'badge' => 'Wedding hall management SaaS',
                'title' => 'Digitize your venue and double revenue',
            'text' => 'Manage bookings, clients and finance in one system without operational chaos.',
            'micro' => 'Every empty date is lost revenue',
            'primary' => 'Get free demo',
            'secondary' => '7-day free trial',
            'tertiary' => 'Connect system',
        ],
        'stats' => [
            ['120+', 'Active venues'],
            ['18k', 'Bookings monthly'],
            ['99.9%', 'Payment control'],
        ],
        'problem_title' => 'Do these problems sound familiar?',
        'problems' => [
            ['Bookings get messy', 'Multiple leads can land on the same date.'],
            ['Busy dates are unclear', 'It becomes hard to see what dates are occupied.'],
            ['Money is invisible', 'You cannot clearly see where revenue is leaking.'],
            ['Admins make mistakes', 'Manual workflows increase costly operational errors.'],
        ],
        'problem_note' => 'These issues can quietly cost your business millions every month',
        'solution_title' => 'Hall management module',
        'solution_text' => 'Run every hall with calendar visibility, booking automation, busy dates and flexible pricing.',
        'solutions' => [
            ['Calendar per hall', 'See each hall by day, week and month.'],
            ['Automated booking flow', 'Requests, confirmations and re-bookings in one workflow.'],
            ['Busy date visibility', 'Spot occupied and free days instantly.'],
            ['Flexible pricing', 'Set pricing rules by hall and date.'],
        ],
        'preview' => [
            'title' => 'A control center built for operators',
            'text' => 'KPI cards, booking schedules, finance trends and team tasks in one modern dark SaaS interface.',
            'revenue' => 'Monthly revenue',
            'occupancy' => 'Occupancy',
            'debt' => 'Outstanding debt',
            'table' => 'Upcoming events',
            'pipeline' => 'Booking pipeline',
        ],
        'transform_title' => 'Everything changes with MyRestoran',
        'transforms' => [
            ['Messy bookings', 'Fully automated workflow'],
            ['Manual accounting', 'Realtime analytics'],
            ['Lost clients', 'Every client under control'],
        ],
        'benefits_title' => 'What the system does for you',
        'benefits' => [
            ['+30%', 'More bookings', 'Fill open dates faster.'],
            ['0 errors', 'Clean calendar', 'Keep dates and booking statuses in sync.'],
            ['24/7', 'Revenue control', 'See payments and profit any time.'],
        ],
        'features_title' => 'Platform capabilities',
        'features' => [
            ['calendar', 'Booking system', 'Manage booked dates and event flow.', 'primary'],
            ['users', 'CRM', 'Keep your client history and communication structured.', 'primary'],
            ['wallet', 'Finance control', 'Track revenue, deposits and cash movement.', 'finance'],
            ['package', 'Inventory', 'Manage products and operational stock.', 'activity'],
            ['chart', 'Analytics', 'Make decisions based on live business data.', 'analytics'],
            ['layout', 'Admin panel', 'Run everything from one control center.', 'primary'],
            ['send', 'Telegram integration', 'Push alerts and notifications instantly.', 'activity'],
        ],
        'money_title' => 'Every empty date means lost revenue',
        'money_text' => 'With MyRestoran, you recover missed bookings and grow revenue with tighter control.',
        'money_highlight' => '+30% more bookings',
        'testimonials_title' => 'Trusted by venue owners',
        'testimonials' => [
            ['Managing bookings became 3x easier with this system.', 'Javohir', 'Wedding Hall owner'],
            ['No more mistakes, everything is finally structured.', 'Nilufar A.', 'Administrator'],
            ['Client communication became faster and team oversight is much easier now.', 'Sardor M.', 'Operations manager'],
        ],
        'pricing_title' => 'Simple and transparent pricing',
        'popular' => 'Most popular',
        'plans' => [
            ['Basic', '$20', '/mo', '1 hall and 1 admin.', ['1 hall', '1 admin', 'Basic booking calendar'], false],
            ['Standard', '$49', '/mo', '3 halls with analytics.', ['3 halls', 'Analytics', 'Finance control', 'Telegram integration'], true],
            ['Premium', '$99', '/mo', 'Unlimited + AI analytics.', ['Unlimited halls', 'AI analytics', 'Advanced reports'], false],
        ],
        'final_title' => 'Start connecting the system',
        'final_text' => 'Take your venue management to a professional level. Start today — tomorrow may be too late.',
        'contact_title' => 'Talk to our team',
        'contact_text' => 'For demos and rollout questions, we respond quickly.',
        'rights' => 'All rights reserved.',
        'demo_request' => 'Request demo',
    ],
];

$c = array_replace_recursive($txt[$fallbackLocale] ?? [], $txt[$locale] ?? []);

if ($locale === 'ru') {
    $c['transform_title'] = $txt['ru']['transform_title'] ?? 'С MyRestoran всё меняется';
    $c['transforms'] = $txt['ru']['transforms'] ?? [
        ['Хаотичные брони', 'Полностью автоматизированная система'],
        ['Ручной учет', 'Аналитика в реальном времени'],
        ['Потерянные клиенты', 'Каждый клиент под контролем'],
    ];
}
$contentOverride = Schema::hasTable('landing_contents')
    ? LandingContent::query()->where('locale', $locale)->first()
    : null;
$dbPlans = Schema::hasTable('subscription_plans')
    ? SubscriptionPlan::query()
        ->when(Schema::hasColumn('subscription_plans', 'is_active'), fn ($query) => $query->where('is_active', true))
        ->orderBy('display_order')
        ->get()
    : collect();

if ($dbPlans->isNotEmpty()) {
    $popularSlug = $dbPlans->firstWhere('slug', 'standard')?->slug
        ?? $dbPlans->firstWhere('slug', 'pro')?->slug
        ?? $dbPlans->skip(1)->first()?->slug
        ?? $dbPlans->first()?->slug;

    $c['plans'] = $dbPlans->map(function (SubscriptionPlan $plan) use ($formatMoney, $formatPlanPeriod, $popularSlug) {
        return [
            $plan->name,
            $formatMoney($plan->amount, $plan->currency ?: 'UZS'),
            $formatPlanPeriod($plan->billing_cycle, $plan->duration_days),
            $plan->description ?: '',
            collect($plan->features ?? [])->filter()->values()->all(),
            $plan->slug === $popularSlug,
        ];
    })->all();
}

if ($contentOverride) {
    $c['hero']['badge'] = $contentOverride->hero_badge ?: $c['hero']['badge'];
    $c['hero']['title'] = $contentOverride->hero_title ?: $c['hero']['title'];
    $c['hero']['text'] = $contentOverride->hero_text ?: $c['hero']['text'];
    $c['hero']['primary'] = $contentOverride->hero_primary_cta ?: $c['hero']['primary'];
    $c['hero']['secondary'] = $contentOverride->hero_secondary_cta ?: $c['hero']['secondary'];
    $c['hero']['micro'] = $contentOverride->hero_microcopy ?: $c['hero']['micro'];
    $c['final_title'] = $contentOverride->final_title ?: $c['final_title'];
    $c['final_text'] = $contentOverride->final_text ?: $c['final_text'];
    $c['contact_title'] = $contentOverride->contact_title ?: $c['contact_title'];
    $c['contact_text'] = $contentOverride->contact_text ?: $c['contact_text'];
}
$c['brand'] = $brandName;
$c['tagline'] = $langText('brand_tagline', $c['tagline']);
$c['meta_title'] = str_replace('MyRestoran', $brandName, $langText('landing_meta_title', $c['meta_title']));
$c['meta_description'] = $langText('landing_meta_description', $c['meta_description']);
$c['nav']['product'] = $langText('landing_nav_product', $c['nav']['product']);
$c['nav']['benefits'] = $langText('landing_nav_benefits', $c['nav']['benefits']);
$c['nav']['pricing'] = $langText('landing_nav_pricing', $c['nav']['pricing']);
$c['nav']['contact'] = $langText('landing_nav_contact', $c['nav']['contact']);
$c['nav']['login'] = $langText('landing_nav_login', $c['nav']['login']);
$c['nav']['register'] = $langText('landing_nav_register', $c['nav']['register']);
$c['transform_title'] = str_replace('MyRestoran', $brandName, $langText('landing_transform_title', $c['transform_title']));
$c['money_text'] = str_replace('MyRestoran', $brandName, $c['money_text']);
$c['benefits_title'] = str_replace('MyRestoran', $brandName, $c['benefits_title']);
$c['transforms'] = [
    [
        $langText('landing_transform_item_1_before', $c['transforms'][0][0] ?? ''),
        $langText('landing_transform_item_1_after', $c['transforms'][0][1] ?? ''),
    ],
    [
        $langText('landing_transform_item_2_before', $c['transforms'][1][0] ?? ''),
        $langText('landing_transform_item_2_after', $c['transforms'][1][1] ?? ''),
    ],
    [
        $langText('landing_transform_item_3_before', $c['transforms'][2][0] ?? ''),
        $langText('landing_transform_item_3_after', $c['transforms'][2][1] ?? ''),
    ],
];
$transformHeadingDefault = $locale === 'ru' ? 'До / После' : ($locale === 'en' ? 'Before / After' : ($locale === 'uzc' ? 'Олдин / Кейин' : 'Before / After'));
$transformBeforeDefault = $locale === 'ru' ? 'До' : ($locale === 'en' ? 'Before' : ($locale === 'uzc' ? 'Олдин' : 'Before'));
$transformAfterDefault = $locale === 'ru' ? 'После' : ($locale === 'en' ? 'After' : ($locale === 'uzc' ? 'Кейин' : 'After'));
$stats = [
    [$c['preview']['revenue'], '$48.7k', '+18.4%'],
    [$c['preview']['occupancy'], '82%', '+12%'],
    [$c['preview']['debt'], '$4.2k', '-21%'],
];
$rows = [
    ['Premium Hall A', '12 May', 'Paid'],
    ['Banket lead', '14 May', 'Deposit'],
    ['Pipeline', '16 May', 'Pending'],
];
$pipe = in_array($locale, ['uz', 'uzc'], true)
    ? [['Lead', 24], ['Muzokara', 18], ['Shartnoma', 11], ["To'lov", 8]]
    : ($locale === 'ru'
        ? [['Лид', 24], ['Переговоры', 18], ['Договор', 11], ['Оплата', 8]]
        : [['Lead', 24], ['Negotiation', 18], ['Contract', 11], ['Payment', 8]]);
    $brandLogo = $resolvedMediaAssets->get('brand_logo');
    $landingDashboard = $resolvedMediaAssets->get('landing_preview_dashboard');
    $landingAdmin = $resolvedMediaAssets->get('landing_preview_admin');
    $landingAnalytics = $resolvedMediaAssets->get('landing_preview_analytics');
@endphp
<x-layouts.landing :title="$c['meta_title']" :description="$c['meta_description']">
<header class="site-header" data-site-header><div class="container shell shell--header"><a href="{{ route('landing',['lang'=>$locale]) }}" class="brand"><span class="brand__mark">@if($brandLogo?->url())<img src="{{ $brandLogo->url() }}" alt="{{ $c['brand'] }}">@else<strong>{{ strtoupper(substr($c['brand'], 0, 2)) }}</strong>@endif</span><span class="brand__meta"><strong>{{ $c['brand'] }}</strong></span></a><nav class="site-nav" data-mobile-nav>@foreach ([['#product',$c['nav']['product']],['#pricing',$c['nav']['pricing']],['#contact',$c['nav']['benefits']]] as [$href,$label])<a href="{{ $href }}">{{ $label }}</a>@endforeach<details class="lang-menu"><summary class="lang-menu__toggle" aria-label="{{ $langText('landing_language_switcher', 'Language switcher') }}"><span class="lang-menu__globe" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M12 22C17.523 22 22 17.523 22 12C22 6.477 17.523 2 12 2C6.477 2 2 6.477 2 12C2 17.523 6.477 22 12 22Z" stroke="currentColor" stroke-width="1.5"/><path d="M8 3.5C6.75 5.72 6 8.74 6 12C6 15.26 6.75 18.28 8 20.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M16 3.5C17.25 5.72 18 8.74 18 12C18 15.26 17.25 18.28 16 20.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M3.5 8H20.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M3.5 16H20.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></span></summary><div class="lang-menu__list">@foreach (['uz'=>'UZ','uzc'=>'УЗ','ru'=>'RU','en'=>'EN'] as $lang => $label)<a href="{{ url()->current() }}?lang={{ $lang }}" class="{{ $lang === $locale ? 'is-active' : '' }}">{{ $label }}</a>@endforeach</div></details></nav><div class="header-actions"><a href="{{ $loginUrl }}" class="button button--ghost">{{ $c['nav']['login'] }}</a><a href="{{ $registerUrl }}" class="button button--primary">{{ $c['nav']['register'] }}</a></div><button class="mobile-nav-toggle" type="button" aria-expanded="false" data-mobile-toggle><span class="sr-only">Toggle navigation</span><span></span><span></span><span></span></button></div></header>
<main id="home">
<section class="hero-section"><div class="hero-orb hero-orb--left"></div><div class="hero-orb hero-orb--right"></div><div class="container hero-grid"><div class="hero-copy"><span class="eyebrow">{{ $c['hero']['badge'] }}</span><h1>{{ $c['hero']['title'] }}</h1><p>{{ $c['hero']['text'] }}</p><div class="hero-micro">{{ $c['hero']['micro'] }}</div><div class="hero-actions"><a href="#contact" class="button button--primary button--large">{{ $c['hero']['primary'] }}</a><a href="#contact" class="button button--secondary button--large">{{ $c['hero']['secondary'] }}</a><a href="{{ $registerUrl }}" class="button button--ghost button--large">{{ $c['hero']['tertiary'] }}</a></div><div class="trust-row"><span>{{ $langText('landing_hero_trust_trial', '7 kun bepul') }}</span><span>{{ $langText('landing_hero_trust_setup', 'Setup 1 kunda') }}</span><span>{{ $langText('landing_hero_trust_demo', 'Demo 15 daqiqada') }}</span></div><div class="hero-stats">@foreach ($c['stats'] as [$value,$label])<article class="stat-pill"><strong>{{ $value }}</strong><span>{{ $label }}</span></article>@endforeach</div></div><div class="hero-preview"><article class="preview-shell"><div class="preview-shell__top"><div class="preview-dots"><span></span><span></span><span></span></div><div class="preview-title">{{ $langText('landing_preview_product_name', $brandName.' OS') }}</div><span class="preview-badge">{{ $langText('landing_preview_badge', 'Live') }}</span></div><div class="preview-metrics">@foreach ($stats as [$label,$value,$delta])<article class="metric-card"><span>{{ $label }}</span><strong>{{ $value }}</strong><small>{{ $delta }}</small></article>@endforeach</div><div class="preview-layout"><section class="preview-card preview-card--chart"><div class="preview-card__head"><h3>{{ $c['preview']['revenue'] }}</h3><span>{{ $langText('landing_preview_chart_period', 'Q1') }}</span></div><div class="chart-bars"><span style="--h:42%"></span><span style="--h:55%"></span><span style="--h:68%"></span><span style="--h:64%"></span><span style="--h:82%"></span><span style="--h:94%"></span></div></section><section class="preview-card preview-card--calendar"><div class="preview-card__head"><h3>{{ $c['preview']['occupancy'] }}</h3><span>{{ $langText('landing_preview_calendar_period', '7 days') }}</span></div><div class="mini-calendar">@foreach ([['Mon','busy'],['Tue','free'],['Wed','busy'],['Thu','free'],['Fri','busy'],['Sat','peak'],['Sun','peak']] as [$day,$state])<div class="mini-calendar__day is-{{ $state }}"><small>{{ $day }}</small></div>@endforeach</div></section><section class="preview-card preview-card--table"><div class="preview-card__head"><h3>{{ $c['preview']['table'] }}</h3><span>{{ $langText('landing_preview_table_items', '3 items') }}</span></div><div class="mini-table">@foreach ($rows as [$event,$date,$status])<div class="mini-table__row"><div><strong>{{ $event }}</strong><small>{{ $date }}</small></div><span>{{ $status }}</span></div>@endforeach</div></section><section class="preview-card preview-card--pipeline"><div class="preview-card__head"><h3>{{ $c['preview']['pipeline'] }}</h3><span>{{ $langText('landing_preview_pipeline_period', 'Today') }}</span></div><div class="pipeline-list">@foreach ($pipe as [$label,$count])<div class="pipeline-list__item"><span>{{ $label }}</span><strong>{{ $count }}</strong></div>@endforeach</div></section></div></article></div></div></section>
<section class="section section--tight"><div class="container"><div class="section-head"><span class="eyebrow">{{ $langText('landing_problem_heading', 'Problem') }}</span><h2>{{ $c['problem_title'] }}</h2><p>{{ $c['problem_note'] }}</p></div><div class="problem-grid">@foreach ($c['problems'] as [$title,$text])<article class="problem-card"><span class="problem-card__icon"></span><h3>{{ $title }}</h3><p>{{ $text }}</p></article>@endforeach</div></div></section>
<section class="section section--soft"><div class="container"><div class="section-head"><span class="eyebrow">{{ $langText('landing_transform_heading', $transformHeadingDefault) }}</span><h2>{{ $c['transform_title'] }}</h2></div><div class="solution-list">@foreach ($c['transforms'] as [$before,$after])<article class="solution-item transformation-card"><small>{{ $langText('landing_transform_before', $transformBeforeDefault) }}</small><h3>{{ $before }}</h3><span class="transformation-arrow">→</span><small>{{ $langText('landing_transform_after', $transformAfterDefault) }}</small><p>{{ $after }}</p></article>@endforeach</div></div></section>
<section class="section" id="product"><div class="container solution-grid"><div class="section-head section-head--left"><span class="eyebrow">{{ $langText('landing_solution_heading', 'Solution') }}</span><h2>{{ $c['solution_title'] }}</h2><p>{{ $c['solution_text'] }}</p></div><div class="solution-list">@foreach ($c['solutions'] as [$title,$text])<article class="solution-item"><h3>{{ $title }}</h3><p>{{ $text }}</p></article>@endforeach</div></div></section>
<section class="section section--dark-panel"><div class="container"><div class="section-head"><span class="eyebrow">{{ $langText('landing_preview_heading', 'Preview') }}</span><h2>{{ $c['preview']['title'] }}</h2><p>{{ $c['preview']['text'] }}</p></div><div class="preview-board"><div class="preview-board__aside">@foreach ($stats as [$label,$value,$delta])<article class="analytics-card"><small>{{ $label }}</small><strong>{{ $value }}</strong><span>{{ $delta }}</span></article>@endforeach</div><div class="preview-board__main"><div class="preview-wide-card"><div class="preview-card__head"><h3>{{ $c['preview']['revenue'] }}</h3><span>{{ $langText('landing_preview_chart_range', 'Last 6 months') }}</span></div><div class="line-chart"><span style="--x:6%;--y:72%"></span><span style="--x:24%;--y:58%"></span><span style="--x:42%;--y:64%"></span><span style="--x:60%;--y:44%"></span><span style="--x:78%;--y:28%"></span><span style="--x:94%;--y:18%"></span></div></div><div class="preview-bottom-grid"><div class="preview-wide-card"><div class="preview-card__head"><h3>{{ $c['preview']['table'] }}</h3><span>{{ $langText('landing_preview_crm_booking', 'CRM + Booking') }}</span></div><div class="mini-table">@foreach ($rows as [$event,$date,$status])<div class="mini-table__row"><div><strong>{{ $event }}</strong><small>{{ $date }}</small></div><span>{{ $status }}</span></div>@endforeach</div></div><div class="preview-wide-card"><div class="preview-card__head"><h3>{{ $c['preview']['pipeline'] }}</h3><span>{{ $langText('landing_preview_realtime', 'Realtime') }}</span></div><div class="pipeline-list">@foreach ($pipe as [$label,$count])<div class="pipeline-list__item"><span>{{ $label }}</span><strong>{{ $count }}</strong></div>@endforeach</div></div></div><div class="preview-placeholders"><div class="preview-placeholder">@if($landingDashboard?->url())<img src="{{ $landingDashboard->url() }}" alt="{{ $landingDashboard->alt_text ?: 'Dashboard screenshot' }}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">@else {{ $langText('landing_preview_placeholder_dashboard', 'Dashboard screenshot') }} @endif</div><div class="preview-placeholder">@if($landingAdmin?->url())<img src="{{ $landingAdmin->url() }}" alt="{{ $landingAdmin->alt_text ?: 'Admin panel UI' }}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">@else {{ $langText('landing_preview_placeholder_admin', 'Admin panel UI') }} @endif</div><div class="preview-placeholder">@if($landingAnalytics?->url())<img src="{{ $landingAnalytics->url() }}" alt="{{ $landingAnalytics->alt_text ?: 'Analytics charts' }}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">@else {{ $langText('landing_preview_placeholder_analytics', 'Analytics charts') }} @endif</div></div></div></div></div></section>
<section class="section" id="benefits"><div class="container"><div class="section-head"><span class="eyebrow">{{ $langText('landing_benefits_heading', 'Benefits') }}</span><h2>{{ $c['benefits_title'] }}</h2></div><div class="benefit-grid">@foreach ($c['benefits'] as [$value,$title,$text])<article class="benefit-card"><strong>{{ $value }}</strong><h3>{{ $title }}</h3><p>{{ $text }}</p></article>@endforeach</div></div></section>
<section class="section section--soft"><div class="container"><div class="section-head"><span class="eyebrow">{{ $langText('landing_features_heading', 'Features') }}</span><h2>{{ $c['features_title'] }}</h2></div><div class="feature-grid">@foreach ($c['features'] as [$icon,$title,$text,$tone])<article class="feature-card feature-card--{{ $tone }}"><span class="feature-card__icon">{!! $iconSvgs[$icon] ?? $iconSvgs['layout'] !!}</span><h3>{{ $title }}</h3><p>{{ $text }}</p></article>@endforeach</div></div></section>
<section class="section"><div class="container"><div class="section-head"><span class="eyebrow">{{ $langText('landing_revenue_heading', 'Revenue') }}</span><h2>{{ $c['money_title'] }}</h2><p>{{ $c['money_text'] }}</p></div><div class="money-banner"><strong>{{ $c['money_highlight'] }}</strong><a href="#contact" class="button button--primary">{{ $c['hero']['primary'] }}</a></div></div></section>
<section class="section" id="testimonials"><div class="container"><div class="section-head"><span class="eyebrow">{{ $langText('landing_social_heading', 'Social proof') }}</span><h2>{{ $c['testimonials_title'] }}</h2></div><div class="testimonial-grid">@foreach ($c['testimonials'] as [$quote,$name,$role])<article class="testimonial-card"><div class="testimonial-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div><p>{{ $quote }}</p><div class="testimonial-meta"><strong>{{ $name }}</strong><span>{{ $role }}</span></div></article>@endforeach</div></div></section>
<section class="section section--dark-panel" id="pricing"><div class="container"><div class="section-head"><span class="eyebrow">{{ $langText('landing_pricing_heading', 'Pricing') }}</span><h2>{{ $c['pricing_title'] }}</h2></div><div class="pricing-grid">@foreach ($c['plans'] as [$name,$price,$period,$text,$items,$popular])<article class="price-card {{ $popular ? 'is-popular' : '' }}">@if($popular)<span class="badge badge--popular">{{ $c['popular'] }}</span>@endif<h3>{{ $name }}</h3><div class="price-card__amount"><strong>{{ $price }}</strong><span>{{ $period }}</span></div>@if(filled($text))<p>{{ $text }}</p>@endif<ul>@foreach(collect($items)->take(6) as $item)<li>{{ $item }}</li>@endforeach</ul><a href="#contact" class="button {{ $popular ? 'button--primary' : 'button--ghost' }}">{{ $c['hero']['primary'] }}</a></article>@endforeach</div></div></section>
<section class="section section--cta"><div class="container"><div class="cta-banner"><div><span class="eyebrow">{{ $c['brand'] }}</span><h2>{{ $c['final_title'] }}</h2><p>{{ $c['final_text'] }}</p><div class="cta-points"><span>{{ $langText('landing_cta_point_demo', 'Bepul demo olish') }}</span><span>{{ $langText('landing_cta_point_trial', '7 kunlik bepul sinov') }}</span><span>{{ $langText('landing_cta_point_consultation', 'Konsultatsiya') }}</span></div></div><div class="cta-banner__actions"><a href="{{ $registerUrl }}" class="button button--primary button--large">{{ $c['nav']['register'] }}</a><a href="#contact" class="button button--secondary button--large">{{ $c['demo_request'] }}</a></div></div></div></section>
<section class="section section--tight" id="contact"><div class="container contact-layout"><div class="section-head section-head--left"><span class="eyebrow">{{ $langText('landing_contact_heading', 'Contact') }}</span><h2>{{ $c['contact_title'] }}</h2><p>{{ $c['contact_text'] }}</p></div><div class="contact-grid"><article class="contact-card"><small>{{ $langText('landing_contact_phone_label', 'Phone') }}</small><strong><a href="tel:{{ preg_replace('/[^0-9+]/', '', $resolvedSetting?->contact_phone ?: '+998 90 777 77 77') }}">{{ $resolvedSetting?->contact_phone ?: '+998 90 777 77 77' }}</a></strong></article><article class="contact-card"><small>{{ $langText('landing_contact_telegram_label', 'Telegram') }}</small><strong><a href="https://t.me/SoliyevNurbek" target="_blank" rel="noopener noreferrer">@SoliyevNurbek</a></strong></article><article class="contact-card"><small>Jamoa Telegram</small><strong><a href="https://t.me/MyRestaurant_SN" target="_blank" rel="noopener noreferrer">@MyRestaurant_SN</a></strong></article></div></div></section>
</main>
<footer class="site-footer"><div class="container footer-grid"><div><div class="brand brand--footer"><span class="brand__mark">@if($brandLogo?->url())<img src="{{ $brandLogo->url() }}" alt="{{ $c['brand'] }}">@else<strong>{{ strtoupper(substr($c['brand'], 0, 2)) }}</strong>@endif</span><span class="brand__meta"><strong>{{ $c['brand'] }}</strong></span></div><p class="footer-copy">{{ $c['meta_description'] }}</p></div><div><h3>{{ $langText('landing_footer_product', 'Product') }}</h3><div class="footer-links"><a href="#product">{{ $c['nav']['product'] }}</a><a href="#benefits">{{ $c['nav']['benefits'] }}</a><a href="#pricing">{{ $c['nav']['pricing'] }}</a></div></div><div><h3>{{ $langText('landing_footer_company', 'Company') }}</h3><div class="footer-links"><a href="#contact">{{ $c['nav']['contact'] }}</a><a href="{{ $loginUrl }}">{{ $c['nav']['login'] }}</a><a href="{{ $registerUrl }}">{{ $c['nav']['register'] }}</a></div></div></div><div class="container footer-bottom"><span>{{ $c['brand'] }}</span><span>{{ date('Y') }} &middot; {{ $langText('landing_footer_rights_suffix', $c['rights']) }}</span></div></footer>
</x-layouts.landing>

