<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\History;

class HistorialController extends Controller
{
    public function index(Request $request)
    {
        $query = History::with(['product', 'user']);

        if ($request->action) {
            $query->where('action', $request->action);
        }

        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        $historial = $query->orderBy('created_at', 'desc')->get()->toArray();

        return view('historial.index', compact('historial'));
    }
}