<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'username' => 'Username atau password salah'
            ])->withInput($request->only('username'));
        }

        Auth::login($user);

        // Redirect berdasarkan role
        if ($user->isAtasan()) {
            return redirect()->route('atasan.dashboard');
        } else {
            return redirect()->route('karyawan.dashboard');
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    public function user()
    {
        if (Auth::check()) {
            return response()->json([
                'user' => Auth::user()->load('kelompok')
            ]);
        }

        return response()->json(['user' => null], 401);
    }
}
