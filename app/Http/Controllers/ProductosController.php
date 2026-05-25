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

        if ($request->status)   $query->where('status',   $request->status);
        if ($request->category) $query->where('category', $request->category);
        if ($request->size)     $query->where('size',     $request->size);
        if ($request->color)    $query->where('color',    $request->color);

        if ($request->shelf && $request->module) {
            $query->whereHas('warehouse', function($q) use ($request) {
                $q->where('shelf',  $request->shelf)
                  ->where('module', $request->module);
            });
        }

        $productos   = $query->orderBy('code')->get()->toArray();
        $categorias  = Product::distinct()->orderBy('category')->pluck('category')->toArray();
        $tallas      = Product::distinct()->orderBy('size')->pluck('size')->toArray();
        $colores     = Product::distinct()->orderBy('color')->pluck('color')->toArray();
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
            'stock'        => 'required|integer|min:1',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'description'  => 'nullable|string',
        ]);

        $stock = $request->stock;

        Product::create([
            'name'             => $request->name,
            'code'             => $request->code,
            'category'         => $request->category,
            'size'             => $request->size,
            'color'            => $request->color,
            'price'            => $request->price,
            'stock'            => $stock,
            'stock_disponible' => $stock,
            'stock_reservado'  => 0,
            'stock_vendido'    => 0,
            'status'           => 'disponible',
            'warehouse_id'     => $request->warehouse_id,
            'description'      => $request->description,
        ]);

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
            'name'         => 'required|string',
            'code'         => 'required|string|unique:products,code,'.$id,
            'category'     => 'required|string',
            'size'         => 'required|string',
            'color'        => 'required|string',
            'price'        => 'required|numeric|min:0',
            'stock'        => 'required|integer|min:0',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'description'  => 'nullable|string',
        ]);

        $nuevoStock = $request->stock;
        $diferencia = $nuevoStock - $product->stock;
        $nuevoDisponible = max(0, $product->stock_disponible + $diferencia);

        $product->update([
            'name'             => $request->name,
            'code'             => $request->code,
            'category'         => $request->category,
            'size'             => $request->size,
            'color'            => $request->color,
            'price'            => $request->price,
            'stock'            => $nuevoStock,
            'stock_disponible' => $nuevoDisponible,
            'warehouse_id'     => $request->warehouse_id,
            'description'      => $request->description,
        ]);

        $product->recalcularEstado();

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

        if ($product->stock_disponible <= 0) {
            return back()->with('error', 'No hay stock disponible.');
        }

        $product->update([
            'stock_disponible' => $product->stock_disponible - 1,
            'stock_reservado'  => $product->stock_reservado  + 1,
        ]);

        $product->recalcularEstado();
        return back()->with('success', 'Producto reservado correctamente.');
    }
}