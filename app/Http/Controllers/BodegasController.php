<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BodegasController extends Controller
{
    private function api(string $method, string $endpoint, array $data = [])
    {
        return Http::withToken(session('token'))
            ->$method(config('app.url').'/api/'.$endpoint, $data);
    }

    public function index()
    {
        $response = $this->api('get', 'warehouses');
        $bodegas  = $response->ok() ? $response->json() : [];

        return view('bodegas.index', compact('bodegas'));
    }

    public function create()
    {
        return view('bodegas.create');
    }

    public function store(Request $request)
    {
        $response = $this->api('post', 'warehouses', $request->except('_token'));

        if ($response->failed()) {
            return back()->withErrors($response->json('errors') ?? ['error' => $response->json('message')])->withInput();
        }

        return redirect()->route('bodegas.index')->with('success', 'Bodega creada correctamente.');
    }

    public function edit(string $id)
    {
        $bodega = $this->api('get', 'warehouses/'.$id)->json();
        return view('bodegas.edit', compact('bodega'));
    }

    public function update(Request $request, string $id)
    {
        $response = $this->api('put', 'warehouses/'.$id, $request->except(['_token', '_method']));

        if ($response->failed()) {
            return back()->withErrors($response->json('errors') ?? ['error' => $response->json('message')])->withInput();
        }

        return redirect()->route('bodegas.index')->with('success', 'Bodega actualizada correctamente.');
    }

    public function destroy(string $id)
    {
        $this->api('delete', 'warehouses/'.$id);
        return redirect()->route('bodegas.index')->with('success', 'Bodega eliminada correctamente.');
    }
}