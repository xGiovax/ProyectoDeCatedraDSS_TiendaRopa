<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\History;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['items.product', 'seller']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->user()->role === 'vendedor') {
            $query->where('seller_id', $request->user()->id);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'notes'         => 'nullable|string',
        ]);

        $order = Order::create([
            'customer_name' => $request->customer_name,
            'seller_id'     => $request->user()->id,
            'status'        => 'pendiente',
            'notes'         => $request->notes,
        ]);

        return response()->json([
            'message' => 'Orden creada correctamente.',
            'order'   => $order
        ], 201);
    }

    public function show(Order $order)
    {
        return response()->json($order->load(['items.product', 'seller']));
    }

    public function addItem(Request $request, Order $order)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        if (!in_array($order->status, ['pendiente', 'en_proceso'])) {
            return response()->json([
                'message' => 'No se pueden agregar productos a esta orden.'
            ], 400);
        }

        $product = Product::findOrFail($request->product_id);

        if ($product->status !== 'reservado') {
            return response()->json([
                'message' => 'El producto debe estar reservado primero.'
            ], 400);
        }

        $existing = OrderItem::where('order_id', $order->id)
                             ->where('product_id', $product->id)
                             ->first();

        if ($existing) {
            return response()->json([
                'message' => 'El producto ya está en la orden.'
            ], 400);
        }

        OrderItem::create([
            'order_id'   => $order->id,
            'product_id' => $product->id,
            'unit_price' => $product->price,
        ]);

        $order->update(['status' => 'en_proceso']);

        return response()->json([
            'message' => 'Producto agregado a la orden.',
            'order'   => $order->load('items.product')
        ]);
    }

    public function removeItem(Request $request, Order $order, OrderItem $item)
    {
        if ($item->order_id !== $order->id) {
            return response()->json(['message' => 'Item no pertenece a esta orden.'], 400);
        }

        $product = $item->product;
        $product->update(['status' => 'disponible']);

        History::create([
            'product_id'  => $product->id,
            'user_id'     => $request->user()->id,
            'action'      => 'liberado',
            'from_status' => 'reservado',
            'to_status'   => 'disponible',
            'notes'       => 'Producto removido de la orden #'.$order->id,
        ]);

        $item->delete();

        return response()->json([
            'message' => 'Producto removido de la orden.'
        ]);
    }

    public function sendToCashier(Request $request, Order $order)
    {
        if ($order->status !== 'en_proceso') {
            return response()->json([
                'message' => 'La orden debe estar en proceso para enviarla a caja.'
            ], 400);
        }

        if ($order->items->isEmpty()) {
            return response()->json([
                'message' => 'La orden no tiene productos.'
            ], 400);
        }

        $order->update(['status' => 'enviada_a_caja']);

        return response()->json([
            'message' => 'Orden enviada a caja correctamente.',
            'order'   => $order->load('items.product')
        ]);
    }

    public function cancel(Request $request, Order $order)
    {
        if (in_array($order->status, ['pagada', 'cancelada'])) {
            return response()->json([
                'message' => 'Esta orden no se puede cancelar.'
            ], 400);
        }

        foreach ($order->items as $item) {
            $item->product->update(['status' => 'disponible']);

            History::create([
                'product_id'  => $item->product_id,
                'user_id'     => $request->user()->id,
                'action'      => 'cancelado',
                'from_status' => 'reservado',
                'to_status'   => 'disponible',
                'notes'       => 'Orden #'.$order->id.' cancelada.',
            ]);
        }

        $order->update(['status' => 'cancelada']);

        return response()->json([
            'message' => 'Orden cancelada correctamente.'
        ]);
    }
}