<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Order;
use App\Models\History;

class SaleController extends Controller
{
    public function index()
    {
        return response()->json(
            Sale::with(['order.items.product', 'cashier'])->get()
        );
    }

    public function process(Request $request, Order $order)
{
    $request->validate([
        'payment_method' => 'required|in:efectivo,tarjeta',
    ]);

    if ($order->status !== 'enviada_a_caja') {
        return response()->json([
            'message' => 'La orden no está lista para pagar o ya fue procesada.'
        ], 400);
    }

    // Verificar que no tenga ya una venta
    if ($order->sale) {
        return response()->json([
            'message' => 'Esta orden ya fue pagada.'
        ], 400);
    }

    $total = $order->items->sum('unit_price');

    $sale = Sale::create([
        'order_id'       => $order->id,
        'cashier_id'     => $request->user()->id,
        'total'          => $total,
        'payment_method' => $request->payment_method,
        'paid_at'        => now(),
    ]);

    foreach ($order->items as $item) {
        SaleItem::create([
            'sale_id'    => $sale->id,
            'product_id' => $item->product_id,
            'unit_price' => $item->unit_price,
        ]);

        $item->product->update(['status' => 'vendido']);

        History::create([
            'product_id'  => $item->product_id,
            'user_id'     => $request->user()->id,
            'action'      => 'vendido',
            'from_status' => 'reservado',
            'to_status'   => 'vendido',
            'notes'       => 'Venta #'.$sale->id.' procesada.',
        ]);
    }

    // Marcar orden como pagada
    $order->update(['status' => 'pagada']);

    return response()->json([
        'message' => 'Pago procesado correctamente.',
        'sale'    => $sale->load('order.items.product')
    ], 201);
}

    public function show(Sale $sale)
    {
        return response()->json(
            $sale->load(['order.items.product', 'cashier'])
        );
    }
}