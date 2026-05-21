<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function dashboard()
    {
        $totalVentas     = Sale::count();
        $ingresoTotal    = Sale::sum('total');
        $productosVendidos = Product::where('status', 'vendido')->count();
        $productosDisponibles = Product::where('status', 'disponible')->count();

        return response()->json([
            'total_ventas'          => $totalVentas,
            'ingreso_total'         => $ingresoTotal,
            'productos_vendidos'    => $productosVendidos,
            'productos_disponibles' => $productosDisponibles,
        ]);
    }

    public function ventasDiarias()
    {
        $ventas = Sale::selectRaw('DATE(paid_at) as fecha, COUNT(*) as total_ventas, SUM(total) as ingresos')
            ->groupBy('fecha')
            ->orderBy('fecha', 'desc')
            ->get();

        return response()->json($ventas);
    }

    public function productosMasVendidos()
    {
        $productos = SaleItem::selectRaw('product_id, COUNT(*) as total_vendido')
            ->with('product')
            ->groupBy('product_id')
            ->orderBy('total_vendido', 'desc')
            ->limit(10)
            ->get();

        return response()->json($productos);
    }

    public function inventario()
    {
        $disponibles = Product::where('status', 'disponible')->count();
        $reservados  = Product::where('status', 'reservado')->count();
        $vendidos    = Product::where('status', 'vendido')->count();

        return response()->json([
            'disponibles' => $disponibles,
            'reservados'  => $reservados,
            'vendidos'    => $vendidos,
        ]);
    }
}