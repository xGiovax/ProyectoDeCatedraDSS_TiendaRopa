<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\History;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = History::with(['product', 'user']);

        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->action) {
            $query->where('action', $request->action);
        }

        return response()->json(
            $query->orderBy('created_at', 'desc')->get()
        );
    }

    public function show(History $history)
    {
        return response()->json($history->load(['product', 'user']));
    }
}