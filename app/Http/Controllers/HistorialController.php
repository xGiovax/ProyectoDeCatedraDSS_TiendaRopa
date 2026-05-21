<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HistorialController extends Controller
{
    private function api(string $method, string $endpoint, array $data = [])
    {
        return Http::withToken(session('token'))
            ->$method(config('app.url').'/api/'.$endpoint, $data);
    }

    public function index(Request $request)
    {
        $params = [];
        if ($request->action)     $params['action']     = $request->action;
        if ($request->product_id) $params['product_id'] = $request->product_id;

        $response  = $this->api('get', 'history', $params);
        $historial = $response->ok() ? $response->json() : [];

        return view('historial.index', compact('historial'));
    }
}