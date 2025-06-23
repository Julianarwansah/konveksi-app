<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Customer;

class AuthController extends Controller
{
    // Menampilkan form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Coba login sebagai admin/user
        if (Auth::guard('web')->attempt($request->only('email', 'password'))) {
            $user = Auth::guard('web')->user();
            Session::put('user', $user);
            return redirect()->route('dashboard');
        }

        // Coba login sebagai customer
        if (Auth::guard('customer')->attempt($request->only('email', 'password'))) {
            $user = Auth::guard('customer')->user();
            Session::put('user', $user);
            return redirect()->route('index');
        }

        // Jika gagal
        return redirect()->route('login')->with('error', 'Email atau password salah!');
    }

    // Proses logout
    public function logout()
    {
        // Logout dari guard yang aktif
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        } elseif (Auth::guard('customer')->check()) {
            Auth::guard('customer')->logout();
        }

        // Hapus session
        Session::forget('user');
        
        return redirect()->route('login')->with('success', 'Anda berhasil logout.');
    }
}