<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function inde(){
        return view('auth.login');
    }

    public function handleLogin(Request $request){
      $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
      ], [
        'email.required' => 'Email harus diisi',
        'email.email' => 'Email tidak valid',
        'password.required' => 'Password harus diisi'
      ]);
      
      if(Auth::attempt($credentials)){
        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
      }

      return back()->withErrors([
        'email' => 'Kredensial yang anda masukkan tidak sesuai'
      ])->onlyInput('email');

    }

    public function logout(Request $request){
      Auth::logout();
      $request->session()->invalidate();
      $request->session()->regenerateToken();
      return redirect('/');
    }
}
