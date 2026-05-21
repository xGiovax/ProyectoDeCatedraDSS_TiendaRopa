<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ReportesController extends Controller
{
    private function api(string $method, string $endpoint, array $data = [])
    {
        return Http::withToken(session('token'))
            ->$method(config('app.url').'/api/'.$endpoint, $data);
    }

    public function index()
    {
        $dashboard   = $this->api('get', 'reports/dashboard')->json();
        $ventasDiarias = $this->api('get', 'reports/ventas-diarias')->json();
        $masVendidos = $this->api('get', 'reports/productos-mas-vendidos')->json();
        $inventario  = $this->api('get', 'reports/inventario')->json();

        return view('reportes.index', compact('dashboard', 'ventasDiarias', 'masVendidos', 'inventario'));
    }
}