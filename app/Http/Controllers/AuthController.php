<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    private const GENERIC_ERROR_MESSAGE = "Login yoki parol notog'ri";

    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        if ($this->isLockedOut($request)) {
            return back()->withErrors([
                'username' => self::GENERIC_ERROR_MESSAGE.'. Iltimos, birozdan keyin qayta urinib ko\'ring.',
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

            return back()->withErrors([
                'username' => self::GENERIC_ERROR_MESSAGE,
            ])->onlyInput('username');
        }

        $this->clearThrottleState($request);
        $this->rotateRememberToken($user);
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        Log::channel('auth')->info('Successful login.', [
            'user_id' => $user->getKey(),
            'identifier' => $request->loginIdentifier(),
            'ip' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 500),
        ]);

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        if ($user = Auth::user()) {
            $this->rotateRememberToken($user);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function resolveUser(string $identifier): ?User
    {
        return User::query()
            ->where('username', $identifier)
            ->orWhere('email', $identifier)
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
}
