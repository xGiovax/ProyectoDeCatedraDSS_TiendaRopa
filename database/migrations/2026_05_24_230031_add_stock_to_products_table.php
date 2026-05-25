<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock')->default(1)->after('price');
            $table->integer('stock_disponible')->default(1)->after('stock');
            $table->integer('stock_reservado')->default(0)->after('stock_disponible');
            $table->integer('stock_vendido')->default(0)->after('stock_reservado');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['stock', 'stock_disponible', 'stock_reservado', 'stock_vendido']);
        });
    }
};