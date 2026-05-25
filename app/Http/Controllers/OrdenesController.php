<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $quantity  = max(1, (int) $request->input('quantity', 1));

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

        if ($product->stock_disponible <= 0) {
            return response()->json(['success' => false, 'message' => 'No hay stock disponible para este producto.'], 400);
        }

        if ($quantity > $product->stock_disponible) {
            return response()->json(['success' => false, 'message' => 'Solo hay '.$product->stock_disponible.' unidades disponibles.'], 400);
        }

        // Verificar si ya existe en la orden y sumar
        $existing = OrderItem::where('order_id', $order->id)
                             ->where('product_id', $product->id)
                             ->first();

        if ($existing) {
            $nuevaCantidad = $existing->quantity + $quantity;
            if ($nuevaCantidad > $product->stock_disponible + $existing->quantity) {
                return response()->json(['success' => false, 'message' => 'No hay suficiente stock.'], 400);
            }
            $existing->update(['quantity' => $nuevaCantidad]);
        } else {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $product->id,
                'quantity'   => $quantity,
                'unit_price' => $product->price,
            ]);
        }

        // Actualizar stock
        $product->update([
            'stock_disponible' => $product->stock_disponible - $quantity,
            'stock_reservado'  => $product->stock_reservado  + $quantity,
        ]);

        $product->recalcularEstado();

        History::create([
            'product_id'  => $product->id,
            'user_id'     => session('user.id'),
            'action'      => 'reservado',
            'from_status' => 'disponible',
            'to_status'   => $product->fresh()->status,
            'notes'       => $quantity.' unidad(es) reservadas para orden #'.$order->id,
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

        $product  = $item->product;
        $quantity = $item->quantity;

        $product->update([
            'stock_disponible' => $product->stock_disponible + $quantity,
            'stock_reservado'  => max(0, $product->stock_reservado - $quantity),
        ]);

        $product->recalcularEstado();

        History::create([
            'product_id'  => $product->id,
            'user_id'     => session('user.id'),
            'action'      => 'liberado',
            'from_status' => 'reservado',
            'to_status'   => 'disponible',
            'notes'       => $quantity.' unidad(es) liberadas de orden #'.$id,
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
            $product  = $item->product;
            $quantity = $item->quantity;

            $product->update([
                'stock_disponible' => $product->stock_disponible + $quantity,
                'stock_reservado'  => max(0, $product->stock_reservado - $quantity),
            ]);

            $product->recalcularEstado();

            History::create([
                'product_id'  => $item->product_id,
                'user_id'     => session('user.id'),
                'action'      => 'cancelado',
                'from_status' => 'reservado',
                'to_status'   => 'disponible',
                'notes'       => $quantity.' unidad(es) liberadas. Orden #'.$order->id.' cancelada.',
            ]);
        }

        $order->update(['status' => 'cancelada']);

        return redirect()->route('ordenes.index')
                         ->with('success', 'Orden cancelada correctamente.');
    }
}