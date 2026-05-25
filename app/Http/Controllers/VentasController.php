<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\History;

class VentasController extends Controller
{
    public function index()
    {
        $ordenes = Order::with(['items.product', 'seller'])
            ->where('status', 'enviada_a_caja')
            ->get()->toArray();

        $ventas = Sale::with(['order.items.product', 'cashier'])
            ->orderBy('created_at', 'desc')
            ->get()->toArray();

        return view('ventas.index', compact('ordenes', 'ventas'));
    }

    public function process(Request $request, string $orderId)
    {
        $order = Order::with('items.product')->find($orderId);

        if (!$order) {
            return back()->with('error', 'Orden no encontrada.');
        }

        if ($order->status !== 'enviada_a_caja') {
            return back()->with('error', 'La orden no está lista para pagar o ya fue procesada.');
        }

        if ($order->sale) {
            return back()->with('error', 'Esta orden ya fue pagada.');
        }

        $total = $order->items->sum(fn($item) => $item->unit_price * $item->quantity);

        $sale = Sale::create([
            'order_id'       => $order->id,
            'cashier_id'     => session('user.id'),
            'total'          => $total,
            'payment_method' => $request->payment_method,
            'paid_at'        => now(),
        ]);

        foreach ($order->items as $item) {
            SaleItem::create([
                'sale_id'    => $sale->id,
                'product_id' => $item->product_id,
                'quantity'   => $item->quantity,
                'unit_price' => $item->unit_price,
            ]);

            $product = $item->product;
            $product->update([
                'stock_reservado' => max(0, $product->stock_reservado - $item->quantity),
                'stock_vendido'   => $product->stock_vendido + $item->quantity,
            ]);

            $product->recalcularEstado();

            History::create([
                'product_id'  => $item->product_id,
                'user_id'     => session('user.id'),
                'action'      => 'vendido',
                'from_status' => 'reservado',
                'to_status'   => $product->fresh()->status,
                'notes'       => $item->quantity.' unidad(es) vendidas. Venta #'.$sale->id,
            ]);
        }

        $order->update(['status' => 'pagada']);

        return redirect()->route('ventas.index')->with('success', 'Pago procesado correctamente.');
    }

    public function cancel(string $orderId)
    {
        $order = Order::with('items.product')->find($orderId);

        if (!$order) {
            return back()->with('error', 'Orden no encontrada.');
        }

        if ($order->status !== 'enviada_a_caja') {
            return back()->with('error', 'Solo se pueden cancelar órdenes enviadas a caja.');
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
                'notes'       => $quantity.' unidad(es) liberadas. Orden #'.$order->id.' cancelada en caja.',
            ]);
        }

        $order->update(['status' => 'cancelada']);

        return redirect()->route('ventas.index')->with('success', 'Orden cancelada correctamente.');
    }

    public function show(string $id)
    {
        $venta = Sale::with(['order.items.product', 'cashier'])->find($id);

        if (!$venta) {
            return redirect()->route('ventas.index')->with('error', 'Venta no encontrada.');
        }

        $venta = $venta->toArray();
        return view('ventas.show', compact('venta'));
    }
}