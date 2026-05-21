<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthWebController extends Controller
{
    public function showLogin()
    {
        if (session('token')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $response = Http::post(config('app.url').'/api/login', [
            'email'    => $request->email,
            'password' => $request->password,
        ]);

        if ($response->failed()) {
            return back()->withErrors([
                'email' => $response->json('message') ?? 'Credenciales incorrectas.'
            ]);
        }

        $data = $response->json();

        session([
            'token' => $data['token'],
            'user'  => $data['user'],
            'role'  => $data['user']['role'],
        ]);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Http::withToken(session('token'))->post(config('app.url').'/api/logout');
        $request->session()->flush();
        return redirect()->route('login');
    }
}