<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UsuariosController extends Controller
{
    private function api(string $method, string $endpoint, array $data = [])
    {
        return Http::withToken(session('token'))
            ->$method(config('app.url').'/api/'.$endpoint, $data);
    }

    public function index()
    {
        $response  = $this->api('get', 'users');
        $usuarios  = $response->ok() ? $response->json() : [];

        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        $response = $this->api('post', 'users', $request->except('_token'));

        if ($response->failed()) {
            return back()->withErrors($response->json('errors') ?? ['error' => $response->json('message')])->withInput();
        }

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(string $id)
    {
        $usuario = $this->api('get', 'users/'.$id)->json();
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, string $id)
    {
        $response = $this->api('put', 'users/'.$id, $request->except(['_token', '_method']));

        if ($response->failed()) {
            return back()->withErrors($response->json('errors') ?? ['error' => $response->json('message')])->withInput();
        }

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $this->api('delete', 'users/'.$id);
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }
}