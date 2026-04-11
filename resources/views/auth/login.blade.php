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
    $restaurantName = $resolvedSetting?->restaurant_name ?? 'MyRestoran';
    $brandLogo = $resolvedMediaAssets->get('brand_logo');
    $copy = match ($locale) {
        'uzc' => [
            'page_title' => 'Кириш',
            'panel' => 'Бошқарув панели',
            'heading' => 'Хуш келибсиз',
            'subtitle' => 'Тизимга кириш учун маълумотларингизни киритинг',
            'username_label' => 'Фойдаланувчи номи',
            'password_label' => 'Парол',
            'remember' => 'Мени эслаб қол',
            'submit' => 'Тизимга кириш',
            'show_password' => 'Паролни кўрсатиш',
            'hide_password' => 'Паролни яшириш',
            'rights' => 'Барча ҳуқуқлар ҳимояланган',
        ],
        'ru' => [
            'page_title' => 'Вход',
            'panel' => 'Панель управления',
            'heading' => 'Добро пожаловать',
            'subtitle' => 'Введите ваши данные для входа в систему',
            'username_label' => 'Имя пользователя',
            'password_label' => 'Пароль',
            'remember' => 'Запомнить меня',
            'submit' => 'Войти в систему',
            'show_password' => 'Показать пароль',
            'hide_password' => 'Скрыть пароль',
            'rights' => 'Все права защищены',
        ],
        'en' => [
            'page_title' => 'Login',
            'panel' => 'Control panel',
            'heading' => 'Welcome back',
            'subtitle' => 'Enter your credentials to access the system',
            'username_label' => 'Username',
            'password_label' => 'Password',
            'remember' => 'Remember me',
            'submit' => 'Sign in',
            'show_password' => 'Show password',
            'hide_password' => 'Hide password',
            'rights' => 'All rights reserved',
        ],
        default => [
            'page_title' => 'Kirish',
            'panel' => 'Boshqaruv paneli',
            'heading' => 'Xush kelibsiz',
            'subtitle' => "Tizimga kirish uchun ma'lumotlaringizni kiriting",
            'username_label' => 'Foydalanuvchi nomi',
            'password_label' => 'Parol',
            'remember' => 'Meni eslab qol',
            'submit' => 'Tizimga kirish',
            'show_password' => "Parolni ko'rsatish",
            'hide_password' => 'Parolni yashirish',
            'rights' => 'Barcha huquqlar himoyalangan',
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
            'username' => 'Введите логин',
            'password' => 'Введите пароль',
        ],
        'uzc' => [
            'username' => 'Username киритинг',
            'password' => 'Парол киритинг',
        ],
        'en' => [
            'username' => 'Enter username',
            'password' => 'Enter password',
        ],
        default => [
            'username' => 'Username kiriting',
            'password' => 'Parol kiriting',
        ],
    };
@endphp

<x-layouts.guest :title="$t('auth_login_page_title', $copy['page_title']).' | '.$restaurantName">
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap');

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    .lp-root {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: radial-gradient(circle at top left, rgba(120,162,255,.05), transparent 30%), linear-gradient(135deg, #07101d, #0d182b);
        font-family: 'Inter', sans-serif;
        padding: 24px;
    }
    .lp-form-panel { width: 100%; max-width: 500px; }
    .lp-form-wrap {
        background: rgba(255, 255, 255, 0.98);
        border-radius: 24px;
        box-shadow: 0 40px 80px rgba(0, 0, 0, 0.4);
        padding: 40px 32px;
        width: 100%;
        position: relative;
        overflow: hidden;
    }
    .lp-form-badge { display: flex; align-items: center; gap: 10px; margin-bottom: 32px; }
    .lp-form-badge-logo {
        width: 56px; height: 56px; border-radius: 20px;
        background: linear-gradient(135deg, #2c6b3f 0%, #1a4228 100%);
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 16px 34px rgba(40,90,55,0.30);
        flex-shrink: 0; overflow: hidden; border: 1px solid rgba(255,255,255,0.16);
    }
    .lp-form-badge-logo img { width: 100%; height: 100%; object-fit: cover; }
    .lp-form-badge-text strong {
        display: block; font-size: 0.95rem; font-weight: 600; color: #1a3324; font-family: 'Playfair Display', serif;
    }
    .lp-form-badge-text span { font-size: 0.75rem; color: #7a9484; }
    .lp-form-heading h1 {
        font-family: 'Playfair Display', serif; font-size: 1.9rem; font-weight: 700; color: #0f2318; letter-spacing: -0.025em; line-height: 1.2;
    }
    .lp-form-sub { font-size: 0.88rem; color: #7a9484; margin: 8px 0 36px; line-height: 1.5; }
    .lp-field { margin-bottom: 18px; }
    .lp-label { display: block; font-size: 0.8rem; font-weight: 600; color: #2d4d39; margin-bottom: 8px; }
    .lp-input-wrap { position: relative; }
    .lp-input-icon {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        width: 18px; height: 18px; color: #8aab94; pointer-events: none;
    }
    .lp-input {
        width: 100%; height: 52px; padding: 0 14px 0 42px; border: 1.5px solid #dce8e0; border-radius: 14px;
        background: #fff; color: #1a3224; font-size: 0.9rem; outline: none; transition: border-color .2s, box-shadow .2s;
    }
    .lp-input:focus { border-color: #3d9954; box-shadow: 0 0 0 4px rgba(61,153,84,.1); }
    .lp-input.has-toggle { padding-right: 48px; }
    .lp-pw-toggle {
        position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
        width: 32px; height: 32px; border: 0; background: transparent; cursor: pointer; color: #8aab94;
        display: flex; align-items: center; justify-content: center; border-radius: 8px;
    }
    .lp-pw-toggle:hover { color: #2c6b3f; background: rgba(61,153,84,.08); }
    .lp-pw-toggle svg { width: 16px; height: 16px; }
    .lp-error { margin-top: 6px; font-size: 0.78rem; color: #dc4545; }
    .lp-options { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
    .lp-remember { display: flex; align-items: center; gap: 8px; cursor: pointer; color: #6a8a75; font-size: 0.82rem; }
    .lp-remember input[type="checkbox"] { width: 16px; height: 16px; accent-color: #3d9954; }
    .lp-submit {
        width: 100%; height: 52px; border: none; border-radius: 14px; background: linear-gradient(135deg, #2d7a43 0%, #1d5230 100%);
        color: #fff; font-size: 0.92rem; font-weight: 600; cursor: pointer; box-shadow: 0 6px 20px rgba(40,100,55,0.3);
        display: flex; align-items: center; justify-content: center; gap: 10px;
        position: relative;
        overflow: hidden;
        line-height: 1;
    }
    .lp-submit:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(40,100,55,0.35); }
    .lp-submit::before,
    .lp-submit::after { content: none !important; }
    .lp-btn-icon {
        width: 18px;
        height: 18px;
        min-width: 18px;
        flex: 0 0 18px;
        display: block;
    }
    .lp-submit-label { line-height: 1; }
    .lp-page-footer {
        margin-top: 18px;
        text-align: center;
        font-size: 0.74rem;
        color: rgba(232, 240, 235, 0.72);
    }
    .lp-back-link {
        margin-top: 16px;
        width: 100%;
        min-height: 52px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 14px 18px;
        border-radius: 14px;
        border: 1px solid rgba(122, 148, 132, 0.2);
        background: rgba(13, 24, 43, 0.82);
        color: #f4faf6;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 600;
        box-shadow: 0 14px 30px rgba(4, 9, 18, 0.2);
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease, background-color .2s ease;
    }
    .lp-back-link:hover {
        transform: translateY(-2px);
        border-color: rgba(127, 190, 149, 0.38);
        background: rgba(16, 33, 56, 0.95);
        box-shadow: 0 18px 36px rgba(4, 9, 18, 0.28);
    }
    .lp-back-link svg {
        width: 14px;
        height: 14px;
        flex: 0 0 14px;
        opacity: 0.9;
    }
    .lp-alert {
        display: flex; align-items: flex-start; gap: 10px; border-radius: 12px; padding: 12px 14px; margin-bottom: 20px; font-size: 0.82rem;
    }
    .lp-alert--error { background: #fff4f4; border: 1px solid #fcc; color: #c03030; }
    .lp-alert--success { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }
    .lp-spinner {
        display: none; width: 18px; height: 18px; border: 2px solid rgba(255,255,255,0.4); border-top-color: white;
        border-radius: 50%; animation: spin 0.7s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    .lp-submit.loading .lp-submit-label { display: none; }
    .lp-submit.loading .lp-spinner { display: block; }
    .lp-submit.loading .lp-btn-icon { display: none; }
</style>

<div class="lp-root">
    <div class="lp-form-panel">
        <div class="lp-form-wrap">
            <div class="lp-form-badge">
                <div class="lp-form-badge-logo">
                    @if($brandLogo?->url())
                        <img src="{{ $brandLogo->url() }}" alt="{{ $restaurantName }}">
                    @else
                        <span style="color:#fff;font-weight:700;">MR</span>
                    @endif
                </div>
                <div class="lp-form-badge-text">
                    <strong>{{ $restaurantName }}</strong>
                    <span>{{ $t('auth_panel_label', $copy['panel']) }}</span>
                </div>
            </div>

            <div class="lp-form-heading">
                <h1>{{ $t('auth_login_heading', $copy['heading']) }}</h1>
            </div>
            <p class="lp-form-sub">{{ $t('auth_login_subtitle', $copy['subtitle']) }}</p>

            @if (session('status'))
                <div class="lp-alert lp-alert--success">
                    <div>{{ session('status') }}</div>
                </div>
            @endif

            @if ($errors->any() && !$errors->has('username') && !$errors->has('password'))
                <div class="lp-alert lp-alert--error">
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('login.store', ['lang' => $locale]) }}" data-loading-form id="loginForm">
                @csrf

                <div class="lp-field">
                    <label class="lp-label" for="username">{{ $t('auth_login_username_label', $copy['username_label']) }}</label>
                    <div class="lp-input-wrap">
                        <svg class="lp-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <input id="username" name="username" type="text" value="{{ old('username') }}" required placeholder="{{ $placeholders['username'] }}" class="lp-input {{ $errors->has('username') ? 'lp-input-err' : '' }}" autocomplete="username" autofocus>
                    </div>
                    @error('username')
                        <div class="lp-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="lp-field">
                    <label class="lp-label" for="password">{{ $t('auth_login_password_label', $copy['password_label']) }}</label>
                    <div class="lp-input-wrap">
                        <svg class="lp-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <input id="password" name="password" type="password" required placeholder="{{ $placeholders['password'] }}" class="lp-input has-toggle {{ $errors->has('password') ? 'lp-input-err' : '' }}" autocomplete="current-password">
                        <button type="button" id="pwToggle" class="lp-pw-toggle" aria-label="{{ $t('auth_login_show_password', $copy['show_password']) }}">
                            <svg id="pwEye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg id="pwEyeOff" style="display:none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                    @error('password')
                        <div class="lp-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="lp-options">
                    <label class="lp-remember">
                        <input type="checkbox" name="remember">
                        <span>{{ $t('auth_login_remember', $copy['remember']) }}</span>
                    </label>
                </div>

                <button type="submit" class="lp-submit" id="submitBtn">
                    <svg class="lp-btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                    <span class="lp-submit-label">{{ $t('auth_login_submit', $copy['submit']) }}</span>
                    <div class="lp-spinner"></div>
                </button>
            </form>

            <a href="{{ $landingUrl }}" class="lp-back-link">
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15.5 10H4.5"/>
                    <path d="M9 5.5 4.5 10 9 14.5"/>
                </svg>
                <span>{{ $t('auth_back_to_landing', $backLabel) }}</span>
            </a>
        </div>

        <div class="lp-page-footer">
            &copy; 2026 {{ $restaurantName }} - {{ $t('auth_rights', $copy['rights']) }}
        </div>
    </div>
</div>

<script>
(() => {
    const pwInput = document.getElementById('password');
    const pwToggle = document.getElementById('pwToggle');
    const pwEye = document.getElementById('pwEye');
    const pwEyeOff = document.getElementById('pwEyeOff');
    const showPasswordLabel = @json($t('auth_login_show_password', $copy['show_password']));
    const hidePasswordLabel = @json($t('auth_login_hide_password', $copy['hide_password']));

    if (pwToggle && pwInput) {
        pwToggle.addEventListener('click', () => {
            const show = pwInput.type === 'password';
            pwInput.type = show ? 'text' : 'password';
            pwEye.style.display = show ? 'none' : '';
            pwEyeOff.style.display = show ? '' : 'none';
            pwToggle.setAttribute('aria-label', show ? hidePasswordLabel : showPasswordLabel);
        });
    }

    const form = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');
    if (form && submitBtn) {
        form.addEventListener('submit', () => {
            submitBtn.disabled = true;
            submitBtn.classList.add('loading');
        });
    }
})();
</script>
</x-layouts.guest>
