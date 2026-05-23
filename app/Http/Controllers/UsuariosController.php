<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsuariosController extends Controller
{
    public function index()
    {
        $usuarios = User::all()->toArray();
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role'     => 'required|in:administrador,vendedor,cajero',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'active'   => true,
        ]);

        return redirect()->route('usuarios.index')
                         ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(string $id)
    {
        $usuario = User::findOrFail($id)->toArray();
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|unique:users,email,'.$id,
            'role'   => 'required|in:administrador,vendedor,cajero',
            'active' => 'required|boolean',
        ]);

        $data = $request->only('name', 'email', 'role', 'active');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('usuarios.index')
                         ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        User::findOrFail($id)->delete();

        return redirect()->route('usuarios.index')
                         ->with('success', 'Usuario eliminado correctamente.');
    }
}