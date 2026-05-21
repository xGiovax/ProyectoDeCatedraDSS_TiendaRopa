<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Warehouse;

class WarehouseController extends Controller
{
    public function index()
    {
        return response()->json(Warehouse::with('products')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'shelf'       => 'required|string',
            'module'      => 'required|string',
            'description' => 'nullable|string',
        ]);

        $warehouse = Warehouse::create($request->all());

        return response()->json([
            'message'   => 'Ubicación creada correctamente.',
            'warehouse' => $warehouse
        ], 201);
    }

    public function show(Warehouse $warehouse)
    {
        return response()->json($warehouse->load('products'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $request->validate([
            'shelf'       => 'sometimes|string',
            'module'      => 'sometimes|string',
            'description' => 'nullable|string',
        ]);

        $warehouse->update($request->all());

        return response()->json([
            'message'   => 'Ubicación actualizada correctamente.',
            'warehouse' => $warehouse
        ]);
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();

        return response()->json([
            'message' => 'Ubicación eliminada correctamente.'
        ]);
    }
}