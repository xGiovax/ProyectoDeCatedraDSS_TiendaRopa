<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        $token = session('token');
        $role  = session('role');

        $stats = [];

        if ($role === 'administrador') {
            $response = Http::withToken($token)
                ->get(config('app.url').'/api/reports/dashboard');
            if ($response->ok()) {
                $stats = $response->json();
            }
        }

        return view('dashboard', compact('stats', 'role'));
    }
}