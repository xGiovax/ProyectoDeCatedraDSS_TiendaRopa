<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\History;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('warehouse');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->category) {
            $query->where('category', $request->category);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('code', 'like', '%'.$request->search.'%');
            });
        }

        return response()->json($query->get());
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

        $product = Product::create($request->all());

        return response()->json([
            'message' => 'Producto creado correctamente.',
            'product' => $product
        ], 201);
    }

    public function show(string $id)
    {
        $product = Product::with('warehouse')->findOrFail($id);

        return response()->json($product);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'         => 'sometimes|string',
            'code'         => 'sometimes|string|unique:products,code,'.$product->id,
            'category'     => 'sometimes|string',
            'size'         => 'sometimes|string',
            'color'        => 'sometimes|string',
            'price'        => 'sometimes|numeric|min:0',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'description'  => 'nullable|string',
        ]);

        $product->update($request->all());

        return response()->json([
            'message' => 'Producto actualizado correctamente.',
            'product' => $product
        ]);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'message' => 'Producto eliminado correctamente.'
        ]);
    }

    public function reserve(Request $request, Product $product)
    {
        if ($product->status !== 'disponible') {
            return response()->json([
                'message' => 'El producto no está disponible para reservar.'
            ], 400);
        }

        $fromStatus = $product->status;
        $product->update(['status' => 'reservado']);

        History::create([
            'product_id'  => $product->id,
            'user_id'     => $request->user()->id,
            'action'      => 'reservado',
            'from_status' => $fromStatus,
            'to_status'   => 'reservado',
            'notes'       => $request->notes ?? null,
        ]);

        return response()->json([
            'message' => 'Producto reservado correctamente.',
            'product' => $product
        ]);
    }
}