<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;

class BodegasController extends Controller
{
    public function index()
    {
        $bodegas = Warehouse::with('products')->get()->toArray();
        return view('bodegas.index', compact('bodegas'));
    }

    public function create()
    {
        return view('bodegas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'shelf'       => 'required|string',
            'module'      => 'required|string',
            'description' => 'nullable|string',
        ]);

        Warehouse::create($request->except('_token'));

        return redirect()->route('bodegas.index')
                         ->with('success', 'Bodega creada correctamente.');
    }

    public function edit(string $id)
    {
        $bodega = Warehouse::findOrFail($id)->toArray();
        return view('bodegas.edit', compact('bodega'));
    }

    public function update(Request $request, string $id)
    {
        $warehouse = Warehouse::findOrFail($id);

        $request->validate([
            'shelf'       => 'required|string',
            'module'      => 'required|string',
            'description' => 'nullable|string',
        ]);

        $warehouse->update($request->except(['_token', '_method']));

        return redirect()->route('bodegas.index')
                         ->with('success', 'Bodega actualizada correctamente.');
    }

    public function destroy(string $id)
    {
        Warehouse::findOrFail($id)->delete();

        return redirect()->route('bodegas.index')
                         ->with('success', 'Bodega eliminada correctamente.');
    }
}