<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrdenesController extends Controller
{
    private function api(string $method, string $endpoint, array $data = [])
    {
        return Http::withToken(session('token'))
            ->$method(config('app.url').'/api/'.$endpoint, $data);
    }

    public function index(Request $request)
    {
        $params = [];
        if ($request->status) $params['status'] = $request->status;

        $response = $this->api('get', 'orders', $params);
        $ordenes  = $response->ok() ? $response->json() : [];

        return view('ordenes.index', compact('ordenes'));
    }

    public function create()
    {
        return view('ordenes.create');
    }

    public function store(Request $request)
    {
        $response = $this->api('post', 'orders', [
            'customer_name' => $request->customer_name,
            'notes'         => $request->notes,
        ]);

        if ($response->failed()) {
            return back()->withErrors(['error' => $response->json('message')])->withInput();
        }

        $orden = $response->json('order');
        return redirect()->route('ordenes.show', $orden['id'])->with('success', 'Orden creada correctamente.');
    }

    public function show(string $id)
    {
        $response = $this->api('get', 'orders/'.$id);
        if ($response->failed()) {
            return redirect()->route('ordenes.index')->with('error', 'Orden no encontrada.');
        }

        $orden = $response->json();
        return view('ordenes.show', compact('orden'));
    }

    public function addItem(Request $request, string $id)
    {
        $response = $this->api('post', 'orders/'.$id.'/items', [
            'product_id' => $request->product_id,
        ]);

        if ($response->failed()) {
            return back()->with('error', $response->json('message'));
        }

        return back()->with('success', 'Producto agregado a la orden.');
    }

    public function removeItem(string $id, string $itemId)
    {
        $this->api('delete', 'orders/'.$id.'/items/'.$itemId);
        return back()->with('success', 'Producto removido de la orden.');
    }

    public function sendToCashier(string $id)
    {
        $response = $this->api('post', 'orders/'.$id.'/send-to-cashier');

        if ($response->failed()) {
            return back()->with('error', $response->json('message'));
        }

        return redirect()->route('ordenes.index')->with('success', 'Orden enviada a caja correctamente.');
    }

    public function cancel(string $id)
    {
        $response = $this->api('post', 'orders/'.$id.'/cancel');

        if ($response->failed()) {
            return back()->with('error', $response->json('message'));
        }

        return redirect()->route('ordenes.index')->with('success', 'Orden cancelada correctamente.');
    }
}