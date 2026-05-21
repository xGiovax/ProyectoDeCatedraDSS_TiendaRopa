<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VentasController extends Controller
{
    private function api(string $method, string $endpoint, array $data = [])
    {
        return Http::withToken(session('token'))
            ->$method(config('app.url').'/api/'.$endpoint, $data);
    }

    public function index()
    {
        $ordenesResponse = $this->api('get', 'orders', ['status' => 'enviada_a_caja']);
        $ordenes = $ordenesResponse->ok() ? $ordenesResponse->json() : [];

        $ventasResponse = $this->api('get', 'sales');
        $ventas = $ventasResponse->ok() ? $ventasResponse->json() : [];

        return view('ventas.index', compact('ordenes', 'ventas'));
    }

    public function process(Request $request, string $orderId)
    {
        $response = $this->api('post', 'orders/'.$orderId.'/process-payment', [
            'payment_method' => $request->payment_method,
        ]);

        if ($response->failed()) {
            return back()->with('error', $response->json('message'));
        }

        return redirect()->route('ventas.index')->with('success', 'Pago procesado correctamente.');
    }

    public function show(string $id)
    {
        $response = $this->api('get', 'sales/'.$id);
        $venta    = $response->ok() ? $response->json() : [];

        return view('ventas.show', compact('venta'));
    }
}