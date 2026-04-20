@php
    $locale = in_array(request('lang', 'uz'), ['uz', 'uzc', 'ru', 'en'], true) ? request('lang', 'uz') : 'uz';
    $pack = $languageLines->get($locale, collect());
    $t = static fn (string $key, string $default) => filled($pack->get($key)) ? $pack->get($key) : $default;
    $resolvedSetting = \Illuminate\Support\Facades\Schema::hasTable('settings')
        ? \App\Models\Setting::global()
        : null;
    $resolvedMediaAssets = \Illuminate\Support\Facades\Schema::hasTable('media_assets')
        ? \App\Models\MediaAsset::keyed()
        : collect();
    $termsPageSlug = \App\Models\Page::TERMS_OF_USE;
    $privacyPageSlug = \App\Models\Page::PRIVACY_POLICY;
    $restaurantName = $resolvedSetting?->restaurant_name ?? 'MyRestoran';
    $brandLogo = $resolvedMediaAssets->get('brand_logo');
    $copy = match ($locale) {
        'uzc' => [
            'page_title' => "Рўйхатдан ўтиш",
            'visual_tag' => 'Venue onboarding',
            'visual_heading' => "Сўров юборинг, суперадмин тасдиқлайди ва тизимга улайди.",
            'visual_text' => "Рўйхатдан ўтган тўйхона аввал текширилади. Тасдиқлангач логин яратилади, вақтинчалик парол берилади ва тизим ишлашга тайёр бўлади.",
            'pending_badge' => 'Pending approval',
            'heading' => "Тўйхонани улаш сўрови",
            'subtitle' => "Қуйидаги маълумотларни юборинг. Суперадмин тасдиқлагандан кейин тизимга кириш маълумотлари берилади.",
            'first_name' => 'Исм',
            'last_name' => 'Фамилия',
            'username' => 'Username',
            'phone' => 'Телефон',
            'restaurant_name' => "Тўйхона номи",
            'message' => "Қўшимча маълумот",
            'terms_prefix' => '',
            'terms_link' => 'Фойдаланиш шартлари',
            'terms_join' => ' ва ',
            'privacy_link' => 'Махфийлик сиёсати',
            'terms_suffix' => 'га розиман.',
            'submit' => "Сўров юбориш",
            'has_account' => 'Ҳисобингиз борми?',
            'login_link' => 'Тизимга киринг',
            'feature_trial_title' => '7 кун бепул синов',
            'feature_trial_text' => "Тизимни хавфсиз тест қилиб кўрасиз.",
            'feature_setup_title' => 'Setup 1 кунда',
            'feature_setup_text' => 'Асосий созламалар ва уланиш тез тайёрланади.',
            'feature_demo_title' => 'Demo 15 дақиқада',
            'feature_demo_text' => "Тақдимот ва бошланғич кўрсатма берилади.",
        ],
        'ru' => [
            'page_title' => 'Регистрация',
            'visual_tag' => 'Подключение площадки',
            'visual_heading' => 'Отправьте заявку, супер админ подтвердит и подключит систему',
            'visual_text' => 'После регистрации площадка проходит проверку. После подтверждения создается логин и выдается временный пароль.',
            'pending_badge' => 'Ожидает подтверждения',
            'heading' => 'Заявка на подключение зала',
            'subtitle' => 'Отправьте данные ниже. После подтверждения супер администратором вы получите доступ в систему.',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'username' => 'Имя пользователя',
            'phone' => 'Телефон',
            'restaurant_name' => 'Название зала',
            'message' => 'Дополнительная информация',
            'terms_prefix' => 'Я согласен с ',
            'terms_link' => 'условиями использования',
            'terms_join' => ' и ',
            'privacy_link' => 'политикой конфиденциальности',
            'terms_suffix' => '.',
            'submit' => 'Отправить заявку',
            'has_account' => 'Уже есть аккаунт?',
            'login_link' => 'Войти в систему',
            'feature_trial_title' => '7 дней бесплатного теста',
            'feature_trial_text' => 'Вы сможете безопасно протестировать систему.',
            'feature_setup_title' => 'Запуск за 1 день',
            'feature_setup_text' => 'Основные настройки и подключение будут быстро подготовлены.',
            'feature_demo_title' => 'Демо за 15 минут',
            'feature_demo_text' => 'Вы получите презентацию и стартовую инструкцию.',
        ],
        'en' => [
            'page_title' => 'Register',
            'visual_tag' => 'Venue onboarding',
            'visual_heading' => 'Send a request and the superadmin will approve and connect your system',
            'visual_text' => 'After registration your venue is reviewed first. Once approved, you can sign in with the same username and password you created.',
            'pending_badge' => 'Pending approval',
            'heading' => 'Venue connection request',
            'subtitle' => 'Submit the details below. After superadmin approval your existing credentials will become active.',
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'username' => 'Username',
            'phone' => 'Phone',
            'restaurant_name' => 'Venue name',
            'message' => 'Additional information',
            'terms_prefix' => 'I agree to the ',
            'terms_link' => 'Terms of Use',
            'terms_join' => ' and ',
            'privacy_link' => 'Privacy Policy',
            'terms_suffix' => '.',
            'submit' => 'Send request',
            'has_account' => 'Already have an account?',
            'login_link' => 'Sign in',
            'feature_trial_title' => '7-day free trial',
            'feature_trial_text' => 'Test the system safely before launch.',
            'feature_setup_title' => 'Setup in 1 day',
            'feature_setup_text' => 'Core settings and connection are prepared quickly.',
            'feature_demo_title' => 'Demo in 15 minutes',
            'feature_demo_text' => 'You get a presentation and quick onboarding guidance.',
        ],
        default => [
            'page_title' => "Ro'yxatdan o'tish",
            'visual_tag' => 'Venue onboarding',
            'visual_heading' => "So'rov yuboring, superadmin tasdiqlaydi va tizimga ulaydi.",
            'visual_text' => "Ro'yxatdan o'tgan to'yxona avval tekshiriladi. Tasdiqlangach siz kiritgan login va parol bilan tizimga kirish faollashadi.",
            'pending_badge' => 'Pending approval',
            'heading' => "To'yxonani ulash so'rovi",
            'subtitle' => "Quyidagi ma'lumotlarni yuboring. Superadmin tasdiqlagach siz yaratgan kirish ma'lumotlari bilan tizimga kira olasiz.",
            'first_name' => 'Ism',
            'last_name' => 'Familiya',
            'username' => 'Username',
            'phone' => 'Telefon',
            'restaurant_name' => "To'yxona nomi",
            'message' => "Qo'shimcha ma'lumot",
            'terms_prefix' => '',
            'terms_link' => 'Foydalanish shartlari',
            'terms_join' => ' va ',
            'privacy_link' => 'Maxfiylik siyosati',
            'terms_suffix' => 'ga roziman.',
            'submit' => "So'rov yuborish",
            'has_account' => 'Hisobingiz bormi?',
            'login_link' => 'Tizimga kiring',
            'feature_trial_title' => '7 kun bepul sinov',
            'feature_trial_text' => "Tizimni xavfsiz test qilib ko'rasiz.",
            'feature_setup_title' => 'Setup 1 kunda',
            'feature_setup_text' => 'Asosiy sozlamalar va ulanish tez tayyorlanadi.',
            'feature_demo_title' => 'Demo 15 daqiqada',
            'feature_demo_text' => "Taqdimot va boshlang'ich ko'rsatma beriladi.",
        ],
    };
    $landingUrl = route('landing', ['lang' => $locale]);
    $backLabel = match ($locale) {
        'ru' => 'Назад',
        'uzc' => 'Ортга',
        'en' => 'Back',
        default => 'Ortga',
    };
    $placeholders = match ($locale) {
        'ru' => [
            'first_name' => 'Введите имя',
            'last_name' => 'Введите фамилию',
            'username' => 'Придумайте логин',
            'phone' => 'Введите телефон',
            'restaurant_name' => 'Введите название',
            'message' => 'Короткий комментарий',
        ],
        'uzc' => [
            'first_name' => 'Исмингизни киритинг',
            'last_name' => 'Фамилиянгизни киритинг',
            'username' => 'Username яратинг',
            'phone' => 'Телефон рақам киритинг',
            'restaurant_name' => "Тўйхона номини киритинг",
            'message' => "Қисқа изоҳ ёзинг",
        ],
        'en' => [
            'first_name' => 'Enter first name',
            'last_name' => 'Enter last name',
            'username' => 'Create username',
            'phone' => 'Enter phone number',
            'restaurant_name' => 'Enter venue name',
            'message' => 'Short note',
        ],
        default => [
            'first_name' => 'Ismingizni kiriting',
            'last_name' => 'Familiyangizni kiriting',
            'username' => 'Username yarating',
            'phone' => 'Telefon raqam kiriting',
            'restaurant_name' => "To'yxona nomini kiriting",
            'message' => "Qisqa izoh yozing",
        ],
    };
    $passwordPlaceholders = match ($locale) {
        'ru' => [
            'password' => 'Введите пароль',
            'password_confirmation' => 'Повторите пароль',
        ],
        'uzc' => [
            'password' => 'Парол киритинг',
            'password_confirmation' => 'Паролни қайта киритинг',
        ],
        'en' => [
            'password' => 'Create password',
            'password_confirmation' => 'Repeat password',
        ],
        default => [
            'password' => 'Parol kiriting',
            'password_confirmation' => 'Parolni qayta kiriting',
        ],
    };
    $passwordHints = match ($locale) {
        'ru' => [
            'default' => 'Минимум 8 символов, буквы, цифры, 1 заглавная буква и 1 спецсимвол.',
            'valid' => 'Пароль соответствует требованиям.',
            'invalid' => 'Добавьте минимум 8 символов, буквы, цифры, 1 заглавную букву и 1 спецсимвол.',
            'confirm_default' => 'Повторите тот же пароль.',
            'confirm_valid' => 'Подтверждение совпадает.',
            'confirm_invalid' => 'Подтверждение пароля не совпадает.',
        ],
        'uzc' => [
            'default' => 'Камида 8 белги, ҳарф, рақам, 1 та катта ҳарф ва 1 та махсус белги киритинг.',
            'valid' => 'Парол талабларга мос.',
            'invalid' => 'Камида 8 белги, ҳарф, рақам, 1 та катта ҳарф ва 1 та махсус белги керак.',
            'confirm_default' => 'Шу паролни қайта киритинг.',
            'confirm_valid' => 'Парол тасдиғи мос келди.',
            'confirm_invalid' => 'Парол тасдиғи мос келмади.',
        ],
        'en' => [
            'default' => 'Use at least 8 characters with letters, numbers, 1 uppercase letter, and 1 special character.',
            'valid' => 'Password meets the requirements.',
            'invalid' => 'Add at least 8 characters, letters, numbers, 1 uppercase letter, and 1 special character.',
            'confirm_default' => 'Repeat the same password.',
            'confirm_valid' => 'Password confirmation matches.',
            'confirm_invalid' => 'Password confirmation does not match.',
        ],
        default => [
            'default' => "Kamida 8 ta belgi, harf, raqam, 1 ta katta harf va 1 ta maxsus belgi kiriting.",
            'valid' => 'Parol talabga mos.',
            'invalid' => "Kamida 8 ta belgi, harf, raqam, 1 ta katta harf va 1 ta maxsus belgi kerak.",
            'confirm_default' => "Shu parolni qayta kiriting.",
            'confirm_valid' => "Parol tasdig'i mos keldi.",
            'confirm_invalid' => "Parol tasdig'i mos kelmadi.",
        ],
    };
    $registrationContext = [
        'source' => old('source', request('source')),
        'entry_point' => old('entry_point', request('entry_point')),
        'selected_plan' => old('selected_plan', request('selected_plan')),
        'recommended_plan' => old('recommended_plan', request('recommended_plan')),
        'halls_count' => old('halls_count', request('halls_count')),
        'monthly_leads' => old('monthly_leads', request('monthly_leads')),
        'selected_role' => old('selected_role', request('selected_role')),
        'selected_scale' => old('selected_scale', request('selected_scale')),
        'selected_timing' => old('selected_timing', request('selected_timing')),
    ];
    $hasRegistrationContext = collect($registrationContext)->filter(fn ($value) => filled($value))->isNotEmpty();
    $contextLabels = [
        'selected_plan' => match ($locale) {
            'en' => 'Selected plan',
            default => 'Tanlangan plan',
        },
        'recommended_plan' => match ($locale) {
            'en' => 'Recommended plan',
            default => 'Tavsiya plan',
        },
        'halls_count' => match ($locale) {
            'en' => 'Halls',
            default => 'Zallar soni',
        },
        'monthly_leads' => match ($locale) {
            'en' => 'Monthly leads',
            default => 'Oylik lead',
        },
        'selected_role' => match ($locale) {
            'en' => 'Role',
            default => 'Rol',
        },
        'selected_scale' => match ($locale) {
            'en' => 'Scale',
            default => 'Setup',
        },
        'selected_timing' => match ($locale) {
            'en' => 'Start timing',
            default => 'Boshlash vaqti',
        },
    ];
    $contextValueMaps = [
        'selected_role' => [
            'owner' => match ($locale) { 'en' => 'Owner', default => "To'yxona egasi" },
            'admin' => match ($locale) { 'en' => 'Administrator', default => 'Administrator' },
            'manager' => match ($locale) { 'en' => 'Manager', default => 'Menejer' },
        ],
        'selected_scale' => [
            'compact' => 'Compact',
            'growth' => 'Growth',
            'scale' => 'Scale',
        ],
        'selected_timing' => [
            'now' => match ($locale) { 'en' => 'This week', default => 'Shu hafta' },
            'month' => match ($locale) { 'en' => 'This month', default => 'Shu oy' },
            'later' => match ($locale) { 'en' => 'Planned', default => 'Rejalashtirilgan' },
        ],
    ];
    $formatContextValue = static function (string $key, mixed $value) use ($contextValueMaps, $locale): string {
        if (! filled($value)) {
            return '';
        }

        if (isset($contextValueMaps[$key][$value])) {
            return $contextValueMaps[$key][$value];
        }

        return match ($key) {
            'halls_count' => $value.' ta zal',
            'monthly_leads' => $value.' lead / oy',
            default => (string) $value,
        };
    };
@endphp

<x-layouts.guest :title="$t('auth_register_page_title', $copy['page_title']).' | '.$restaurantName">
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap');

    *, *::before, *::after { box-sizing: border-box; }
    .register-shell {
        min-height: 100vh; display: flex; align-items: center; justify-content: center;
        background: radial-gradient(circle at top left, rgba(120,162,255,.05), transparent 30%), linear-gradient(135deg, #07101d, #0d182b);
        padding: 32px 18px; font-family: 'Inter', sans-serif;
    }
    .register-card {
        width: min(100%, 1120px); display: grid; grid-template-columns: 1fr 1.05fr; overflow: hidden;
        border-radius: 32px; background: rgba(255,255,255,0.04); box-shadow: 0 40px 80px rgba(0,0,0,0.35);
    }
    .register-visual {
        position: relative; background: linear-gradient(160deg, #08150c 0%, #0e2417 48%, #09140d 100%); color: white;
        padding: 48px; display: flex; flex-direction: column; justify-content: space-between; overflow: hidden;
    }
    .register-brand, .register-feature-list, .register-copy { position: relative; z-index: 1; }
    .register-brand { display: flex; align-items: center; gap: 14px; }
    .register-brand-mark {
        width: 60px; height: 60px; border-radius: 22px; background: linear-gradient(135deg, #3d9954, #256836);
        display: flex; align-items: center; justify-content: center; overflow: hidden;
        box-shadow: 0 18px 36px rgba(61,153,84,0.35); font-weight: 700; border: 1px solid rgba(255,255,255,.14);
    }
    .register-brand-mark img { width: 100%; height: 100%; object-fit: cover; }
    .register-brand h1 { font-family: 'Playfair Display', serif; font-size: 1.3rem; margin: 0; }
    .register-brand p { margin: 4px 0 0; font-size: .78rem; letter-spacing: .12em; text-transform: uppercase; color: rgba(255,255,255,.5); }
    .register-copy h2 { margin: 18px 0 14px; font-family: 'Playfair Display', serif; font-size: clamp(2rem, 3vw, 3rem); line-height: 1.12; }
    .register-copy p { margin: 0; max-width: 420px; line-height: 1.75; color: rgba(255,255,255,.68); }
    .register-context-card {
        margin-bottom: 20px; padding: 18px 18px 16px; border-radius: 20px;
        border: 1px solid rgba(61,153,84,.18); background: linear-gradient(180deg, #f6fbf7 0%, #eef5f0 100%);
        box-shadow: 0 16px 34px rgba(16, 37, 25, .08);
    }
    .register-context-card small {
        display: inline-flex; align-items: center; gap: 8px; margin-bottom: 10px;
        font-size: .72rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: #2f7a45;
    }
    .register-context-card h3 { margin: 0 0 8px; color: #12311e; font-size: 1rem; }
    .register-context-card p { margin: 0; color: #597065; font-size: .9rem; line-height: 1.6; }
    .register-context-grid {
        margin-top: 14px; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px;
    }
    .register-context-item {
        padding: 12px 13px; border-radius: 16px; background: rgba(255,255,255,.7); border: 1px solid rgba(47,122,69,.08);
    }
    .register-context-item span { display: block; margin-bottom: 4px; font-size: .72rem; color: #6c8378; }
    .register-context-item strong { color: #183b25; font-size: .92rem; }
    .register-feature-list { display: grid; gap: 12px; margin-top: 34px; }
    .register-feature { border: 1px solid rgba(255,255,255,.08); background: rgba(255,255,255,.05); border-radius: 18px; padding: 16px 18px; }
    .register-feature strong { display: block; margin-bottom: 6px; }
    .register-feature span { font-size: .9rem; color: rgba(255,255,255,.62); }
    .register-form-panel { background: rgba(255,255,255,.98); padding: 38px 32px; }
    .register-form-badge {
        display: inline-flex; align-items: center; gap: 8px; border-radius: 999px; border: 1px solid rgba(61,153,84,.18);
        background: rgba(61,153,84,.08); padding: 7px 14px; font-size: .74rem; letter-spacing: .08em; text-transform: uppercase; color: #2f7a45;
    }
    .register-form-title { margin: 18px 0 8px; font-family: 'Playfair Display', serif; font-size: 2rem; color: #102519; }
    .register-form-sub { margin: 0 0 28px; color: #708678; line-height: 1.65; font-size: .92rem; }
    .register-grid { display: grid; gap: 16px; }
    .register-grid.two { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .register-field label { display: block; margin-bottom: 8px; font-size: .82rem; font-weight: 600; color: #2c4a36; }
    .register-field input, .register-field textarea {
        width: 100%; border: 1.5px solid #d9e6dd; border-radius: 16px; background: #fff; color: #183123; font: inherit;
        padding: 14px 16px; outline: none; transition: border-color .2s, box-shadow .2s;
    }
    .register-input-wrap { position: relative; }
    .register-input-wrap input { padding-right: 50px; }
    .register-password-toggle {
        position: absolute; top: 50%; right: 14px; transform: translateY(-50%);
        width: 30px; height: 30px; border: 0; background: transparent; color: #6b7f73;
        display: inline-flex; align-items: center; justify-content: center; cursor: pointer; padding: 0;
    }
    .register-password-toggle:hover { color: #1d5230; }
    .register-password-toggle:focus-visible {
        outline: 0; border-radius: 999px; box-shadow: 0 0 0 4px rgba(61,153,84,.12);
    }
    .register-password-toggle svg { width: 18px; height: 18px; }
    .register-field textarea { min-height: 118px; resize: vertical; }
    .register-field input:focus, .register-field textarea:focus { border-color: #3d9954; box-shadow: 0 0 0 4px rgba(61,153,84,.12); }
    .register-alert { margin-bottom: 18px; border-radius: 16px; padding: 12px 14px; font-size: .84rem; }
    .register-alert.success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
    .register-alert.error { background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }
    .register-terms { display: flex; align-items: flex-start; gap: 10px; border: 1px solid #e2ece6; background: #f8fbf9; border-radius: 16px; padding: 14px 16px; color: #667d6f; font-size: .87rem; }
    .register-terms input { margin-top: 2px; accent-color: #2f7a45; }
    .register-terms a { color: #1f6f3f; font-weight: 700; text-decoration: none; }
    .register-terms a:hover { text-decoration: underline; }
    .register-submit {
        width: 100%; height: 52px; border: 0; border-radius: 16px; background: linear-gradient(135deg, #2d7a43 0%, #1d5230 100%);
        color: white; font-weight: 600; cursor: pointer; box-shadow: 0 12px 28px rgba(40,100,55,.28);
        transition: opacity .2s ease, filter .2s ease, box-shadow .2s ease, transform .2s ease;
    }
    .register-submit:hover { filter: brightness(1.03); }
    .register-submit:disabled {
        opacity: .48;
        cursor: not-allowed;
        filter: saturate(.55);
        box-shadow: none;
    }
    .register-submit:disabled:hover { filter: saturate(.55); }
    .register-footer { margin-top: 18px; text-align: center; font-size: .88rem; color: #72867a; }
    .register-footer a { color: #2f7a45; text-decoration: none; font-weight: 600; }
    .register-back-link {
        margin-top: 16px;
        width: 100%;
        min-height: 52px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 14px 18px;
        border-radius: 16px;
        border: 1px solid #d9e6dd;
        background: linear-gradient(180deg, #f8fbf9 0%, #eef5f0 100%);
        color: #1d5230;
        text-decoration: none;
        font-size: .9rem;
        font-weight: 600;
        box-shadow: 0 10px 24px rgba(16, 37, 25, .08);
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease, color .2s ease;
    }
    .register-back-link:hover {
        transform: translateY(-2px);
        border-color: rgba(61,153,84,.35);
        color: #184527;
        box-shadow: 0 14px 28px rgba(16, 37, 25, .12);
    }
    .register-back-link svg {
        width: 14px;
        height: 14px;
        flex: 0 0 14px;
        opacity: .9;
    }
    .register-error { margin-top: 6px; color: #dc2626; font-size: .78rem; }
    .register-hint { margin-top: 6px; color: #6b7f73; font-size: .78rem; line-height: 1.45; }
    .register-hint.is-valid { color: #15803d; }
    .register-hint.is-invalid { color: #dc2626; }
    @media (max-width: 960px) {
        .register-card { grid-template-columns: 1fr; }
        .register-visual { padding: 32px 24px; }
        .register-form-panel { padding: 32px 22px; }
    }
    @media (max-width: 640px) {
        .register-grid.two { grid-template-columns: 1fr; }
        .register-context-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="register-shell">
    <div class="register-card">
        <section class="register-visual">
            <div class="register-brand">
                <div class="register-brand-mark">
                    @if($brandLogo?->url())
                        <img src="{{ $brandLogo->url() }}" alt="{{ $restaurantName }}">
                    @else
                        MR
                    @endif
                </div>
                <div>
                    <h1>{{ $restaurantName }}</h1>
                    <p>{{ $t('auth_register_visual_tag', $copy['visual_tag']) }}</p>
                </div>
            </div>

            <div class="register-copy">
                <div class="register-form-badge">{{ $t('auth_register_pending_badge', $copy['pending_badge']) }}</div>
                <h2>{{ $t('auth_register_visual_heading', $copy['visual_heading']) }}</h2>
                <p>{{ $t('auth_register_visual_text', $copy['visual_text']) }}</p>
                <div class="register-feature-list">
                    <div class="register-feature"><strong>{{ $t('auth_register_feature_trial_title', $copy['feature_trial_title']) }}</strong><span>{{ $t('auth_register_feature_trial_text', $copy['feature_trial_text']) }}</span></div>
                    <div class="register-feature"><strong>{{ $t('auth_register_feature_setup_title', $copy['feature_setup_title']) }}</strong><span>{{ $t('auth_register_feature_setup_text', $copy['feature_setup_text']) }}</span></div>
                    <div class="register-feature"><strong>{{ $t('auth_register_feature_demo_title', $copy['feature_demo_title']) }}</strong><span>{{ $t('auth_register_feature_demo_text', $copy['feature_demo_text']) }}</span></div>
                </div>
            </div>
        </section>

        <section class="register-form-panel">
            <div class="register-form-badge">{{ $t('landing_nav_register', $copy['page_title']) }}</div>
            <h2 class="register-form-title">{{ $t('auth_register_heading', $copy['heading']) }}</h2>
            <p class="register-form-sub">{{ $t('auth_register_subtitle', $copy['subtitle']) }}</p>

            @if (session('status'))
                <div class="register-alert success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="register-alert error">{{ $errors->first() }}</div>
            @endif

            @if ($hasRegistrationContext)
                <div class="register-context-card">
                    <small>{{ $locale === 'en' ? 'Landing recommendation' : 'Landing tavsiyasi' }}</small>
                    <h3>{{ $locale === 'en' ? 'Your onboarding path is prefilled' : "Siz uchun onboarding yo'li tayyorlandi" }}</h3>
                    <p>{{ $locale === 'en' ? 'Continue registration with the plan and setup signals selected on the landing page.' : "Landing sahifasida tanlangan plan, setup va start signallari registratsiyaga olib o'tildi." }}</p>
                    <div class="register-context-grid">
                        @foreach (['selected_plan', 'recommended_plan', 'halls_count', 'monthly_leads', 'selected_role', 'selected_scale', 'selected_timing'] as $contextKey)
                            @if (filled($registrationContext[$contextKey]))
                                <div class="register-context-item">
                                    <span>{{ $contextLabels[$contextKey] }}</span>
                                    <strong>{{ $formatContextValue($contextKey, $registrationContext[$contextKey]) }}</strong>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('register.store', ['lang' => $locale]) }}" class="register-grid" id="registerForm">
                @csrf
                @foreach ($registrationContext as $contextKey => $contextValue)
                    <input type="hidden" name="{{ $contextKey }}" value="{{ $contextValue }}">
                @endforeach

                <div class="register-grid two">
                    <div class="register-field">
                        <label for="first_name">{{ $t('auth_register_first_name', $copy['first_name']) }}</label>
                        <input id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="{{ $placeholders['first_name'] }}" autocomplete="given-name" required>
                        @error('first_name')<div class="register-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="register-field">
                        <label for="last_name">{{ $t('auth_register_last_name', $copy['last_name']) }}</label>
                        <input id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="{{ $placeholders['last_name'] }}" autocomplete="family-name" required>
                        @error('last_name')<div class="register-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="register-grid two">
                    <div class="register-field">
                        <label for="username">{{ $t('auth_register_username', $copy['username']) }}</label>
                        <input id="username" name="username" value="{{ old('username') }}" placeholder="{{ $placeholders['username'] }}" autocomplete="username" autocapitalize="none" spellcheck="false" required>
                        @error('username')<div class="register-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="register-field">
                        <label for="phone">{{ $t('auth_register_phone', $copy['phone']) }}</label>
                        <input id="phone" name="phone" value="{{ old('phone') }}" placeholder="{{ $placeholders['phone'] }}" inputmode="tel" autocomplete="tel">
                        @error('phone')<div class="register-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="register-grid two">
                    <div class="register-field">
                        <label for="password">{{ $t('auth_register_password', 'Parol') }}</label>
                        <div class="register-input-wrap">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                placeholder="{{ $passwordPlaceholders['password'] }}"
                                autocomplete="new-password"
                                required
                            >
                            <button class="register-password-toggle" type="button" data-password-toggle="password" aria-label="{{ $t('auth_register_toggle_password', "Parolni ko'rsatish yoki yashirish") }}">
                                <svg data-password-icon="show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg data-password-icon="hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" hidden>
                                    <path d="m3 3 18 18"/>
                                    <path d="M10.58 10.58A2 2 0 0 0 13.42 13.42"/>
                                    <path d="M9.88 5.09A10.94 10.94 0 0 1 12 4.88c6 0 9.75 7.12 9.75 7.12a20.64 20.64 0 0 1-4.04 4.95"/>
                                    <path d="M6.61 6.61A20.61 20.61 0 0 0 2.25 12s3.75 6.75 9.75 6.75a10.7 10.7 0 0 0 4.12-.82"/>
                                </svg>
                            </button>
                        </div>
                        <div
                            class="register-hint"
                            data-password-hint
                            data-default-text="{{ $passwordHints['default'] }}"
                            data-valid-text="{{ $passwordHints['valid'] }}"
                            data-invalid-text="{{ $passwordHints['invalid'] }}"
                        >{{ $passwordHints['default'] }}</div>
                        @error('password')<div class="register-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="register-field">
                        <label for="password_confirmation">{{ $t('auth_register_password_confirmation', 'Parolni tasdiqlash') }}</label>
                        <div class="register-input-wrap">
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                placeholder="{{ $passwordPlaceholders['password_confirmation'] }}"
                                autocomplete="new-password"
                                required
                            >
                            <button class="register-password-toggle" type="button" data-password-toggle="password_confirmation" aria-label="{{ $t('auth_register_toggle_password', "Parolni ko'rsatish yoki yashirish") }}">
                                <svg data-password-icon="show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg data-password-icon="hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" hidden>
                                    <path d="m3 3 18 18"/>
                                    <path d="M10.58 10.58A2 2 0 0 0 13.42 13.42"/>
                                    <path d="M9.88 5.09A10.94 10.94 0 0 1 12 4.88c6 0 9.75 7.12 9.75 7.12a20.64 20.64 0 0 1-4.04 4.95"/>
                                    <path d="M6.61 6.61A20.61 20.61 0 0 0 2.25 12s3.75 6.75 9.75 6.75a10.7 10.7 0 0 0 4.12-.82"/>
                                </svg>
                            </button>
                        </div>
                        <div
                            class="register-hint"
                            data-password-confirmation-hint
                            data-default-text="{{ $passwordHints['confirm_default'] }}"
                            data-valid-text="{{ $passwordHints['confirm_valid'] }}"
                            data-invalid-text="{{ $passwordHints['confirm_invalid'] }}"
                        >{{ $passwordHints['confirm_default'] }}</div>
                        @error('password_confirmation')<div class="register-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="register-field">
                    <label for="restaurant_name">{{ $t('auth_register_restaurant_name', $copy['restaurant_name']) }}</label>
                    <input id="restaurant_name" name="restaurant_name" value="{{ old('restaurant_name') }}" placeholder="{{ $placeholders['restaurant_name'] }}" autocomplete="organization" required>
                    @error('restaurant_name')<div class="register-error">{{ $message }}</div>@enderror
                </div>

                <div class="register-field">
                    <label for="message">{{ $t('auth_register_message', $copy['message']) }}</label>
                    <textarea id="message" name="message" placeholder="{{ $placeholders['message'] }}">{{ old('message') }}</textarea>
                    @error('message')<div class="register-error">{{ $message }}</div>@enderror
                </div>

                <label class="register-terms">
                    <input type="checkbox" name="terms" required data-terms-toggle {{ old('terms') ? 'checked' : '' }}>
                    <span>
                        {{ $copy['terms_prefix'] }}
                        <a href="{{ route('pages.show', ['slug' => $termsPageSlug]) }}" target="_blank" rel="noopener noreferrer">{{ $copy['terms_link'] }}</a>
                        {{ $copy['terms_join'] }}
                        <a href="{{ route('pages.show', ['slug' => $privacyPageSlug]) }}" target="_blank" rel="noopener noreferrer">{{ $copy['privacy_link'] }}</a>{{ $copy['terms_suffix'] }}
                    </span>
                </label>
                @error('terms')<div class="register-error">{{ $message }}</div>@enderror
 
                <button class="register-submit" type="submit" data-submit-button>{{ $t('auth_register_submit', $copy['submit']) }}</button>
            </form>

            <a href="{{ $landingUrl }}" class="register-back-link">
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15.5 10H4.5"/>
                    <path d="M9 5.5 4.5 10 9 14.5"/>
                </svg>
                <span>{{ $t('auth_back_to_landing', $backLabel) }}</span>
            </a>
            <div class="register-footer">
                {{ $t('auth_register_has_account', $copy['has_account']) }}
                <a href="{{ route('login', ['lang' => $locale]) }}">{{ $t('auth_register_login_link', $copy['login_link']) }}</a>
            </div>
        </section>
    </div>
</div>
<script>
    (() => {
        const form = document.getElementById('registerForm');
        if (!form) return;

        const termsToggle = form.querySelector('[data-terms-toggle]');
        const submitButton = form.querySelector('[data-submit-button]');
        const passwordToggles = form.querySelectorAll('[data-password-toggle]');
        const passwordInput = form.querySelector('#password');
        const passwordConfirmationInput = form.querySelector('#password_confirmation');
        const passwordHint = form.querySelector('[data-password-hint]');
        const passwordConfirmationHint = form.querySelector('[data-password-confirmation-hint]');

        if (passwordToggles.length) {
            passwordToggles.forEach((toggle) => {
                toggle.addEventListener('click', () => {
                    const input = form.querySelector(`#${toggle.dataset.passwordToggle}`);
                    if (!input) return;

                    const nextType = input.type === 'password' ? 'text' : 'password';
                    input.type = nextType;

                    toggle.querySelector('[data-password-icon="show"]')?.toggleAttribute('hidden', nextType === 'text');
                    toggle.querySelector('[data-password-icon="hide"]')?.toggleAttribute('hidden', nextType === 'password');
                });
            });
        }

        const setHintState = (element, state) => {
            if (!element) return;

            const text = element.dataset[`${state}Text`] ?? element.dataset.defaultText ?? '';
            element.textContent = text;
            element.classList.toggle('is-valid', state === 'valid');
            element.classList.toggle('is-invalid', state === 'invalid');
        };

        const passwordMeetsRules = (value) => {
            return value.length >= 8
                && /[A-Za-z]/.test(value)
                && /\d/.test(value)
                && /[A-Z]/.test(value)
                && /[^A-Za-z0-9]/.test(value);
        };

        const syncPasswordHints = () => {
            if (passwordInput && passwordHint) {
                if (passwordInput.value.length === 0) {
                    setHintState(passwordHint, 'default');
                } else {
                    setHintState(passwordHint, passwordMeetsRules(passwordInput.value) ? 'valid' : 'invalid');
                }
            }

            if (passwordInput && passwordConfirmationInput && passwordConfirmationHint) {
                if (passwordConfirmationInput.value.length === 0) {
                    setHintState(passwordConfirmationHint, 'default');
                } else {
                    setHintState(passwordConfirmationHint, passwordInput.value === passwordConfirmationInput.value ? 'valid' : 'invalid');
                }
            }
        };

        passwordInput?.addEventListener('input', syncPasswordHints);
        passwordInput?.addEventListener('blur', syncPasswordHints);
        passwordConfirmationInput?.addEventListener('input', syncPasswordHints);
        passwordConfirmationInput?.addEventListener('blur', syncPasswordHints);
        syncPasswordHints();

        if (!termsToggle || !submitButton) return;

        const syncSubmitState = () => {
            submitButton.disabled = !termsToggle.checked;
        };

        syncSubmitState();
        termsToggle.addEventListener('change', syncSubmitState);
    })();
</script>
</x-layouts.guest>
