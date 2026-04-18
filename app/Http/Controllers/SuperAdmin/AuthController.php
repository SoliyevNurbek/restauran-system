<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (auth()->check() && auth()->user()?->role === 'superadmin') {
            return redirect()->route('superadmin.dashboard');
        }

        return view('superadmin.auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $user = User::query()
            ->where('role', 'superadmin')
            ->where('username', $request->loginIdentifier())
            ->first();

        if (! $user || ! Hash::check((string) $request->validated('password'), $user->password)) {
            return back()->withErrors(['username' => $request->genericMessage()])->onlyInput('username');
        }

        if (($user->status ?? 'active') !== 'active') {
            return back()->withErrors(['username' => $request->genericMessage()])->onlyInput('username');
        }

        $this->rotateRememberToken($user);

        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return redirect()->route('superadmin.dashboard');
    }

    private function rotateRememberToken(User $user): void
    {
        $user->forceFill([
            'remember_token' => Str::random(60),
        ])->saveQuietly();
    }
}
