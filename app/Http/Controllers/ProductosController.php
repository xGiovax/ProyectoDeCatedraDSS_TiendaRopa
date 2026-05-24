<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Warehouse;

class ProductosController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('warehouse');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('code', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->category) {
            $query->where('category', $request->category);
        }

        if ($request->size) {
            $query->where('size', $request->size);
        }

        if ($request->color) {
            $query->where('color', $request->color);
        }

        if ($request->shelf && $request->module) {
            $query->whereHas('warehouse', function($q) use ($request) {
                $q->where('shelf', $request->shelf)
                  ->where('module', $request->module);
            });
        }

        $productos = $query->orderBy('code')->get()->toArray();

        // Datos para los filtros dinámicos
        $categorias = Product::distinct()->orderBy('category')->pluck('category')->toArray();
        $tallas     = Product::distinct()->orderBy('size')->pluck('size')->toArray();
        $colores    = Product::distinct()->orderBy('color')->pluck('color')->toArray();
        $ubicaciones = Warehouse::orderBy('shelf')->orderBy('module')->get()->toArray();

        return view('productos.index', compact('productos', 'categorias', 'tallas', 'colores', 'ubicaciones'));
    }

    public function create()
    {
        $bodegas = Warehouse::all()->toArray();
        return view('productos.create', compact('bodegas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string',
            'code'         => 'required|string|unique:products',
            'category'     => 'required|string',
            'size'         => 'required|string',
            'color'        => 'required|string',
            'price'        => 'required|numeric|min:0',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'description'  => 'nullable|string',
        ]);

        Product::create($request->except('_token'));

        return redirect()->route('productos.index')
                         ->with('success', 'Producto creado correctamente.');
    }

    public function edit(string $id)
    {
        $producto = Product::with('warehouse')->findOrFail($id)->toArray();
        $bodegas  = Warehouse::all()->toArray();
        return view('productos.edit', compact('producto', 'bodegas'));
    }

    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name'         => 'sometimes|string',
            'code'         => 'sometimes|string|unique:products,code,'.$id,
            'category'     => 'sometimes|string',
            'size'         => 'sometimes|string',
            'color'        => 'sometimes|string',
            'price'        => 'sometimes|numeric|min:0',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'description'  => 'nullable|string',
        ]);

        $product->update($request->except(['_token', '_method']));

        return redirect()->route('productos.index')
                         ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        Product::findOrFail($id)->delete();

        return redirect()->route('productos.index')
                         ->with('success', 'Producto eliminado correctamente.');
    }

    public function reserve(string $id)
    {
        $product = Product::findOrFail($id);

        if ($product->status !== 'disponible') {
            return back()->with('error', 'El producto no está disponible para reservar.');
        }

        $product->update(['status' => 'reservado']);

        return back()->with('success', 'Producto reservado correctamente.');
    }
}