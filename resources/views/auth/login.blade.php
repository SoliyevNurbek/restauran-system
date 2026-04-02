@php
    $restaurantName = $appSetting->restaurant_name ?? 'Javohir Restoran';
@endphp

<x-layouts.guest :title="'Kirish | ' . $restaurantName">
    <style>
        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 16px;
            background:
                radial-gradient(circle at top, rgba(255, 255, 255, 0.92), transparent 34%),
                linear-gradient(180deg, #eef2ea 0%, #e6ede4 100%);
        }

        .login-shell {
            width: 100%;
            max-width: 500px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 18px;
        }

        .login-logo-wrap {
            width: fit-content;
            margin: 0 auto 18px;
            padding: 10px 18px;
            border-radius: 24px;
            background: #ffffff;
            box-shadow: 0 10px 28px rgba(43, 61, 49, 0.06);
        }

        .login-logo {
            width: min(230px, 58vw);
            height: auto;
            display: block;
            mix-blend-mode: multiply;
        }

        .login-title {
            max-width: 420px;
            margin: 0;
            color: #223d2f;
            font-size: clamp(1.2rem, 2.4vw, 1.55rem);
            line-height: 1.18;
            font-weight: 600;
            letter-spacing: -0.03em;
            font-family: Georgia, "Times New Roman", serif;
            margin-inline: auto;
        }

        .login-subtitle {
            margin: 8px 0 0;
            color: #6c7f73;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .login-card {
            width: 100%;
            padding: 22px 22px 16px;
            border-radius: 32px;
            border: 1px solid rgba(99, 118, 103, 0.15);
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 22px 60px rgba(43, 61, 49, 0.08);
            backdrop-filter: blur(8px);
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .login-field label {
            display: block;
            margin-bottom: 7px;
            color: #314d3d;
            font-size: 0.88rem;
            font-weight: 600;
        }

        .login-input {
            width: 100%;
            border: 1px solid #d8e0d5;
            border-radius: 16px;
            background: #fdfefd;
            color: #21382c;
            padding: 13px 15px;
            font-size: 0.92rem;
            line-height: 1.4;
            outline: none;
            transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
        }

        .login-input::placeholder {
            color: #9cac9f;
        }

        .login-input:focus {
            border-color: #55775f;
            box-shadow: 0 0 0 4px rgba(85, 119, 95, 0.12);
            background: #ffffff;
        }

        .login-password-wrap {
            position: relative;
        }

        .login-password-wrap .login-input {
            padding-right: 48px;
        }

        .login-password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border: 0;
            border-radius: 999px;
            background: transparent;
            color: #6c7f73;
            cursor: pointer;
            transition: background-color .2s ease, color .2s ease;
        }

        .login-password-toggle:hover {
            background: rgba(85, 119, 95, 0.08);
            color: #355943;
        }

        .login-submit {
            width: 100%;
            border: 0;
            border-radius: 16px;
            padding: 14px 18px;
            background: linear-gradient(180deg, #355943 0%, #294636 100%);
            color: #ffffff;
            font-size: 0.94rem;
            font-weight: 600;
            letter-spacing: 0.01em;
            box-shadow: 0 14px 28px rgba(41, 70, 54, 0.22);
            transition: transform .2s ease, box-shadow .2s ease, opacity .2s ease;
        }

        .login-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 32px rgba(41, 70, 54, 0.24);
        }

        .login-submit:active {
            transform: translateY(0);
        }

        .login-error {
            margin-top: 8px;
            font-size: 0.8rem;
            color: #dc2626;
        }

        .login-footer {
            margin-top: 14px;
            text-align: center;
            color: #90a193;
            font-size: 0.72rem;
            letter-spacing: 0.01em;
        }

        @media (max-width: 640px) {
            .login-page {
                padding: 16px 12px;
            }

            .login-card {
                padding: 18px 16px 14px;
                border-radius: 26px;
            }

            .login-logo-wrap {
                padding: 8px 14px;
                margin-bottom: 14px;
            }

            .login-logo {
                width: min(190px, 56vw);
            }
        }
    </style>

    <div class="login-page">
        <div class="login-shell">
            <div class="login-card">
                <div class="login-header">
                    <div class="login-logo-wrap">
                        <img src="{{ asset('Javohirlogo.png') }}" alt="{{ $restaurantName }}" class="login-logo">
                    </div>

                    <h1 class="login-title">{{ $restaurantName }} Boshqaruv Paneli</h1>
                    <p class="login-subtitle">Jarayonlarni nazorat qiling</p>
                </div>

                <form method="POST" action="{{ route('login.store') }}" class="login-form" data-loading-form>
                    @csrf

                    <div class="login-field">
                        <label for="username">Login</label>
                        <input
                            id="username"
                            name="username"
                            type="text"
                            value="{{ old('username') }}"
                            required
                            placeholder="Login"
                            class="login-input"
                        >
                        @error('username')<p class="login-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="login-field">
                        <label for="password">Parol</label>
                        <div class="login-password-wrap">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                placeholder="Parol"
                                class="login-input"
                            >
                            <button type="button" id="passwordToggle" class="login-password-toggle" aria-label="Parolni ko'rsatish">
                                <i data-lucide="eye" id="passwordToggleShow" class="h-4 w-4"></i>
                                <i data-lucide="eye-off" id="passwordToggleHide" class="hidden h-4 w-4"></i>
                            </button>
                        </div>
                        @error('password')<p class="login-error">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="login-submit">
                        Tizimga kirish
                    </button>
                </form>

                <div class="login-footer">2026 {{ $restaurantName }} | Barcha huquqlar himoyalangan</div>
            </div>
        </div>
    </div>
    <script>
        (() => {
            const passwordInput = document.getElementById('password');
            const passwordToggle = document.getElementById('passwordToggle');
            const passwordToggleShow = document.getElementById('passwordToggleShow');
            const passwordToggleHide = document.getElementById('passwordToggleHide');

            if (!passwordInput || !passwordToggle || !passwordToggleShow || !passwordToggleHide) return;

            passwordToggle.addEventListener('click', () => {
                const isPassword = passwordInput.type === 'password';

                passwordInput.type = isPassword ? 'text' : 'password';
                passwordToggle.setAttribute('aria-label', isPassword ? 'Parolni yashirish' : 'Parolni ko\'rsatish');
                passwordToggleShow.classList.toggle('hidden', isPassword);
                passwordToggleHide.classList.toggle('hidden', !isPassword);
            });
        })();
    </script>
</x-layouts.guest>

