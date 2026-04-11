<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->route('superadmin.dashboard');
    }
}
