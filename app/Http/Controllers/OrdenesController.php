<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\History;

class OrdenesController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['items.product', 'seller']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if (session('role') === 'vendedor') {
            $query->where('seller_id', session('user.id'));
        }

        $ordenes = $query->orderBy('created_at', 'desc')->get()->toArray();

        return view('ordenes.index', compact('ordenes'));
    }

    public function create()
    {
        return view('ordenes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
        ]);

        $order = Order::create([
            'customer_name' => $request->customer_name,
            'seller_id'     => session('user.id'),
            'status'        => 'pendiente',
            'notes'         => $request->notes,
        ]);

        return redirect()->route('ordenes.show', $order->id)
                         ->with('success', 'Orden creada correctamente.');
    }

    public function show(string $id)
    {
        $order = Order::with(['items.product', 'seller'])->find($id);

        if (!$order) {
            return redirect()->route('ordenes.index')->with('error', 'Orden no encontrada.');
        }

        $orden = $order->toArray();
        return view('ordenes.show', compact('orden'));
    }

    public function addItem(Request $request, string $id)
    {
        $productId = $request->input('product_id');

        if (!$productId) {
            return response()->json(['success' => false, 'message' => 'No se recibió el producto.'], 400);
        }

        $order = Order::find($id);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Orden no encontrada.'], 400);
        }

        if (!in_array($order->status, ['pendiente', 'en_proceso'])) {
            return response()->json(['success' => false, 'message' => 'No se pueden agregar productos a esta orden.'], 400);
        }

        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Producto no encontrado.'], 400);
        }

        if ($product->status !== 'disponible') {
            return response()->json(['success' => false, 'message' => 'El producto no está disponible. Estado: '.$product->status], 400);
        }

        $existing = OrderItem::where('order_id', $order->id)
                             ->where('product_id', $product->id)
                             ->first();

        if ($existing) {
            return response()->json(['success' => false, 'message' => 'El producto ya está en la orden.'], 400);
        }

        $product->update(['status' => 'reservado']);

        History::create([
            'product_id'  => $product->id,
            'user_id'     => session('user.id'),
            'action'      => 'reservado',
            'from_status' => 'disponible',
            'to_status'   => 'reservado',
            'notes'       => 'Reservado al agregar a orden #'.$order->id,
        ]);

        OrderItem::create([
            'order_id'   => $order->id,
            'product_id' => $product->id,
            'unit_price' => $product->price,
        ]);

        $order->update(['status' => 'en_proceso']);

        return response()->json(['success' => true, 'message' => 'Producto agregado correctamente.']);
    }

    public function removeItem(string $id, string $itemId)
    {
        $item = OrderItem::find($itemId);

        if (!$item || $item->order_id != $id) {
            return back()->with('error', 'Item no encontrado.');
        }

        $product = $item->product;
        $product->update(['status' => 'disponible']);

        History::create([
            'product_id'  => $product->id,
            'user_id'     => session('user.id'),
            'action'      => 'liberado',
            'from_status' => 'reservado',
            'to_status'   => 'disponible',
            'notes'       => 'Producto removido de la orden #'.$id,
        ]);

        $item->delete();

        return back()->with('success', 'Producto removido de la orden.');
    }

    public function sendToCashier(string $id)
    {
        $order = Order::with('items')->find($id);

        if (!$order) {
            return back()->with('error', 'Orden no encontrada.');
        }

        if ($order->status !== 'en_proceso') {
            return back()->with('error', 'La orden debe estar en proceso para enviarla a caja.');
        }

        if ($order->items->isEmpty()) {
            return back()->with('error', 'La orden no tiene productos.');
        }

        $order->update(['status' => 'enviada_a_caja']);

        return redirect()->route('ordenes.index')
                         ->with('success', 'Orden enviada a caja correctamente.');
    }

    public function cancel(string $id)
    {
        $order = Order::with('items.product')->find($id);

        if (!$order) {
            return back()->with('error', 'Orden no encontrada.');
        }

        if (in_array($order->status, ['pagada', 'cancelada'])) {
            return back()->with('error', 'Esta orden no se puede cancelar.');
        }

        foreach ($order->items as $item) {
            $item->product->update(['status' => 'disponible']);

            History::create([
                'product_id'  => $item->product_id,
                'user_id'     => session('user.id'),
                'action'      => 'cancelado',
                'from_status' => 'reservado',
                'to_status'   => 'disponible',
                'notes'       => 'Orden #'.$order->id.' cancelada.',
            ]);
        }

        $order->update(['status' => 'cancelada']);

        return redirect()->route('ordenes.index')
                         ->with('success', 'Orden cancelada correctamente.');
    }
}