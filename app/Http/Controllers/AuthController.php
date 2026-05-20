<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function loginForm(): View|RedirectResponse
    {
        if (auth()->check()) {
            return $this->redirectAfterLogin();
        }
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (! auth()->attempt([
            'username'  => $credentials['username'],
            'password'  => $credentials['password'],
            'is_active' => true,
        ], $request->boolean('remember'))) {
            return back()->withInput($request->only('username'))
                ->with('error', 'Login yoki parol noto\'g\'ri.');
        }

        $request->session()->regenerate();
        session(['shop_id' => auth()->user()->shop_id]);

        return $this->redirectAfterLogin();
    }

    public function logout(Request $request): RedirectResponse
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function redirectAfterLogin(): RedirectResponse
    {
        if (auth()->user()->isManagerOrAdmin()) {
            return redirect()->route('backend.dashboard');
        }
        return redirect()->route('pos.index');
    }
}
