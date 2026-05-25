<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'code', 'category', 'size', 'color',
        'price', 'stock', 'stock_disponible', 'stock_reservado',
        'stock_vendido', 'status', 'warehouse_id', 'description'
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function history()
    {
        return $this->hasMany(History::class);
    }

    // Recalcula el estado según el stock
    public function recalcularEstado(): void
    {
        if ($this->stock_disponible > 0) {
            $status = 'disponible';
        } elseif ($this->stock_reservado > 0) {
            $status = 'reservado';
        } else {
            $status = 'vendido';
        }
        $this->update(['status' => $status]);
    }
}