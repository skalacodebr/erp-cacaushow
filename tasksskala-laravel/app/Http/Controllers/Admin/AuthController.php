<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        // Se já está logado, redireciona para o dashboard
        if (session('admin_authenticated')) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = $request->email;
        $password = $request->password;

        // Credenciais do admin hardcoded
        if ($email === 'admin@skalacode.com.br' && $password === 'Rs5$1ge0') {
            session(['admin_authenticated' => true, 'admin_email' => $email]);
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        session()->forget(['admin_authenticated', 'admin_email']);
        session()->flush();
        
        return redirect()->route('admin.login')->with('message', 'Logout realizado com sucesso!');
    }
}