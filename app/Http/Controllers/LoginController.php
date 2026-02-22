<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function actionLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        // jika user ditemukan / berhasil login

        if (auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard')->with('success', 'login-berhasil');
        } else {
            return back()->withErrors(['email' => 'Email atau password salah'])->onlyInput('email');
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('login');
    }
}
