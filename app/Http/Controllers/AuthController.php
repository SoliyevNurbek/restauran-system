<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\BusinessSubscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\VenueConnection;
use App\Services\SuperAdmin\AdminNotificationService;
use App\Services\SuperAdmin\SecurityEventService;
use App\Services\SuperAdmin\TelegramNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{

    public function showLogin(): View|RedirectResponse
    {
        return view('auth.login');
    }

    public function showRegister(): View|RedirectResponse
    {
        return view('auth.register');
    }

    public function register(
        RegisterRequest $request,
        AdminNotificationService $notifications,
        TelegramNotificationService $telegram,
    ): RedirectResponse
    {
        $locale = $this->locale($request);
        $data = $request->validated();

        DB::transaction(function () use ($data, $notifications, $telegram) {
            $venue = VenueConnection::create([
                'venue_name' => $data['restaurant_name'],
                'owner_name' => trim($data['first_name'].' '.$data['last_name']),
                'username' => $data['username'],
                'phone' => $data['phone'] ?? null,
                'message' => $data['message'] ?? null,
                'status' => 'pending',
                'health_status' => 'new',
            ]);

            $user = User::create([
                'name' => $venue->owner_name,
                'username' => $venue->username,
                'password' => Hash::make($data['password']),
                'role' => 'admin',
                'status' => 'pending',
                'venue_connection_id' => $venue->id,
            ]);

            $plan = Schema::hasTable('subscription_plans')
                ? SubscriptionPlan::query()
                    ->when(
                        Schema::hasColumn('subscription_plans', 'is_active'),
                        fn ($query) => $query->where('is_active', true)
                    )
                    ->orderByRaw("CASE WHEN slug = 'basic' THEN 0 ELSE 1 END")
                    ->orderBy('display_order')
                    ->first()
                : null;

            if ($plan && Schema::hasTable('business_subscriptions')) {
                $trialEndsAt = now()->addDays(config('billing.trial_days', 7));

                BusinessSubscription::query()->create([
                    'venue_connection_id' => $venue->getKey(),
                    'user_id' => $user->getKey(),
                    'subscription_plan_id' => $plan->getKey(),
                    'status' => 'trial',
                    'activity_state' => 'healthy',
                    'billing_cycle' => $plan->billing_cycle,
                    'amount' => $plan->amount,
                    'currency' => $plan->currency,
                    'auto_renew' => false,
                    'starts_at' => now(),
                    'trial_ends_at' => $trialEndsAt,
                    'renews_at' => $trialEndsAt,
                    'expires_at' => $trialEndsAt,
                    'notes' => 'Auto-created after registration request.',
                ]);
            }

            if (Schema::hasTable('admin_notifications')) {
                $notifications->create(
                    type: 'new_business_registration',
                    title: "Yangi biznes ro'yxatdan o'tdi",
                    description: $venue->venue_name." moderatsiya navbatiga qo'shildi.",
                    status: 'warning',
                    icon: 'building-2',
                    actionUrl: route('superadmin.approvals.index'),
                    relatedType: $venue::class,
                    relatedId: $venue->getKey(),
                    sendTelegram: true,
                    telegramMessage: $telegram->format(
                        heading: 'MyRestaurant_SN',
                        eventType: 'New business registration',
                        subject: $venue->venue_name,
                        lines: [
                            'Owner' => $venue->owner_name,
                            'Telefon' => $venue->phone,
                            'Username' => $venue->username,
                        ],
                    ),
                );
            }
        });

        return redirect()->route('login', ['lang' => $locale])->with('status', $this->text('register_success', $locale));
    }

    public function login(LoginRequest $request, SecurityEventService $security): RedirectResponse
    {
        $locale = $this->locale($request);

        if ($this->isLockedOut($request)) {
            return back()->withErrors([
                'username' => $this->text('invalid_login', $locale).'. '.$this->text('try_again_later', $locale),
            ])->onlyInput('username');
        }

        $user = $this->resolveUser($request->loginIdentifier());
        $passwordValid = Hash::check(
            (string) $request->validated('password'),
            $user?->password ?? $this->fallbackPasswordHash()
        );

        if (! $user || ! $passwordValid) {
            $this->recordFailure($request);
            $this->applyBackoff($request);
            $security->record(
                eventType: 'failed_login',
                title: 'Muvaffaqiyatsiz login urinish',
                description: $request->loginIdentifier(),
                severity: 'warning',
                ip: $request->ip(),
                userAgent: $request->userAgent(),
                meta: ['identifier' => $request->loginIdentifier()],
            );

            return back()->withErrors([
                'username' => $this->text('invalid_login', $locale),
            ])->onlyInput('username');
        }

        if (($user->status ?? 'active') !== 'active') {
            return back()->withErrors([
                'username' => $this->text('account_pending', $locale),
            ])->onlyInput('username');
        }

        $this->clearThrottleState($request);
        $this->rotateRememberToken($user);

        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $request->session()->regenerateToken();
        if (Schema::hasColumn('users', 'last_login_at') && Schema::hasColumn('users', 'last_login_ip')) {
            $user->forceFill([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ])->saveQuietly();
        }

        Log::channel('auth')->info('Successful login.', [
            'user_id' => $user->getKey(),
            'identifier' => $request->loginIdentifier(),
            'ip' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 500),
        ]);

        $security->record(
            eventType: 'successful_login',
            title: 'Muvaffaqiyatli login',
            description: $user->username,
            user: $user,
            venue: $user->venueConnection,
            severity: 'info',
            ip: $request->ip(),
            userAgent: $request->userAgent(),
            meta: ['role' => $user->role],
        );

        return redirect()->intended(route($user->role === 'superadmin' ? 'superadmin.dashboard' : 'dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        if ($user = Auth::user()) {
            $this->rotateRememberToken($user);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }

    protected function resolveUser(string $identifier): ?User
    {
        return User::query()
            ->where('username', $identifier)
            ->first();
    }

    protected function isLockedOut(LoginRequest $request): bool
    {
        return RateLimiter::tooManyAttempts($request->throttleKey(), 5)
            || now()->timestamp < (int) cache()->get($request->lockoutKey(), 0);
    }

    protected function recordFailure(LoginRequest $request): void
    {
        RateLimiter::hit($request->throttleKey(), 60);

        $failures = cache()->increment($request->failuresKey());
        cache()->put($request->failuresKey(), $failures, now()->addDay());

        $lockoutSeconds = match (true) {
            $failures >= 20 => 900,
            $failures >= 10 => 300,
            $failures >= 5 => 60,
            default => 0,
        };

        if ($lockoutSeconds > 0) {
            cache()->put($request->lockoutKey(), now()->addSeconds($lockoutSeconds)->timestamp, now()->addSeconds($lockoutSeconds));
        }

        Log::channel('auth')->warning('Failed login attempt.', [
            'identifier' => $request->loginIdentifier(),
            'ip' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 500),
            'failures' => $failures,
            'rate_limited' => RateLimiter::tooManyAttempts($request->throttleKey(), 5),
        ]);
    }

    protected function clearThrottleState(LoginRequest $request): void
    {
        RateLimiter::clear($request->throttleKey());
        cache()->forget($request->lockoutKey());
        cache()->forget($request->failuresKey());
    }

    protected function applyBackoff(LoginRequest $request): void
    {
        $failures = min((int) cache()->get($request->failuresKey(), 1), 5);
        usleep($failures * 200000);
    }

    protected function rotateRememberToken(User $user): void
    {
        $user->forceFill([
            'remember_token' => Str::random(60),
        ])->saveQuietly();
    }

    protected function fallbackPasswordHash(): string
    {
        return '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
    }

    private function locale(Request $request): string
    {
        $locale = (string) $request->query('lang', 'uz');

        return in_array($locale, ['uz', 'uzc', 'ru', 'en'], true) ? $locale : 'uz';
    }

    private function text(string $key, string $locale): string
    {
        $messages = [
            'uz' => [
                'invalid_login' => "Login yoki parol notog'ri",
                'try_again_later' => "Iltimos, birozdan keyin qayta urinib ko'ring",
                'account_pending' => "Hisobingiz hali tasdiqlanmagan yoki vaqtincha bloklangan.",
                'register_success' => "Ro'yxatdan o'tish muvaffaqiyatli yakunlandi. So'rovingiz superadmin tasdig'idan so'ng faollashadi.",
            ],
            'uzc' => [
                'invalid_login' => "Login yoki parol noto'g'ri",
                'try_again_later' => "Iltimos, birozdan keyin qayta urinib ko'ring",
                'account_pending' => "Hisobingiz hali tasdiqlanmagan yoki vaqtincha bloklangan.",
                'register_success' => "Ro'yxatdan o'tish muvaffaqiyatli yakunlandi. So'rovingiz superadmin tasdig'idan so'ng faollashadi.",
            ],
            'ru' => [
                'invalid_login' => 'Неверный логин или пароль',
                'try_again_later' => 'Пожалуйста, попробуйте еще раз немного позже',
                'account_pending' => 'Ваш аккаунт еще не подтвержден или временно заблокирован.',
                'register_success' => 'Ваша заявка принята. После подтверждения супер администратором вы получите данные для входа.',
            ],
            'en' => [
                'invalid_login' => 'Invalid login or password',
                'try_again_later' => 'Please try again a little later',
                'account_pending' => 'Your account has not been approved yet or is temporarily blocked.',
                'register_success' => 'Registration completed successfully. Your request will become active after superadmin approval.',
            ],
        ];

        return $messages[$locale][$key] ?? $messages['uz'][$key] ?? $key;
    }
}
