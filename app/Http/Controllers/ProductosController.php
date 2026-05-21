<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProductosController extends Controller
{
    private function api(string $method, string $endpoint, array $data = [])
    {
        return Http::withToken(session('token'))
            ->$method(config('app.url').'/api/'.$endpoint, $data);
    }

    public function index(Request $request)
    {
        $params = [];
        if ($request->search)   $params['search']   = $request->search;
        if ($request->status)   $params['status']   = $request->status;
        if ($request->category) $params['category'] = $request->category;

        $response = $this->api('get', 'products', $params);
        $productos = $response->ok() ? $response->json() : [];

        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        $bodegas = $this->api('get', 'warehouses')->json();
        return view('productos.create', compact('bodegas'));
    }

    public function store(Request $request)
    {
        $response = $this->api('post', 'products', $request->except('_token'));

        if ($response->failed()) {
            return back()->withErrors($response->json('errors') ?? ['error' => $response->json('message')])->withInput();
        }

        return redirect()->route('productos.index')->with('success', 'Producto creado correctamente.');
    }

    public function edit(string $id)
    {
        $producto = $this->api('get', 'products/'.$id)->json();
        $bodegas  = $this->api('get', 'warehouses')->json();
        return view('productos.edit', compact('producto', 'bodegas'));
    }

    public function update(Request $request, string $id)
    {
        $response = $this->api('put', 'products/'.$id, $request->except(['_token', '_method']));

        if ($response->failed()) {
            return back()->withErrors($response->json('errors') ?? ['error' => $response->json('message')])->withInput();
        }

        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $this->api('delete', 'products/'.$id);
        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente.');
    }
}