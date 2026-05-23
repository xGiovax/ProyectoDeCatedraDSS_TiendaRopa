<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleItem;

class ReportesController extends Controller
{
    public function index()
    {
        $dashboard = [
            'total_ventas'          => Sale::count(),
            'ingreso_total'         => Sale::sum('total'),
            'productos_vendidos'    => Product::where('status', 'vendido')->count(),
            'productos_disponibles' => Product::where('status', 'disponible')->count(),
        ];

        $ventasDiarias = Sale::selectRaw('DATE(paid_at) as fecha, COUNT(*) as total_ventas, SUM(total) as ingresos')
            ->groupBy('fecha')
            ->orderBy('fecha', 'desc')
            ->get()
            ->toArray();

        $masVendidos = SaleItem::selectRaw('product_id, COUNT(*) as total_vendido')
            ->with('product')
            ->groupBy('product_id')
            ->orderBy('total_vendido', 'desc')
            ->limit(10)
            ->get()
            ->toArray();

        $inventario = [
            'disponibles' => Product::where('status', 'disponible')->count(),
            'reservados'  => Product::where('status', 'reservado')->count(),
            'vendidos'    => Product::where('status', 'vendido')->count(),
        ];

        return view('reportes.index', compact('dashboard', 'ventasDiarias', 'masVendidos', 'inventario'));
    }
}