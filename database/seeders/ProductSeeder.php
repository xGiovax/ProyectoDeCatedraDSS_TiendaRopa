<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Camiseta Básica', 'code' => 'CAM-001', 'category' => 'Camisetas', 'size' => 'S', 'color' => 'Blanco', 'price' => 15.99, 'status' => 'disponible', 'warehouse_id' => 1],
            ['name' => 'Camiseta Básica', 'code' => 'CAM-002', 'category' => 'Camisetas', 'size' => 'M', 'color' => 'Blanco', 'price' => 15.99, 'status' => 'disponible', 'warehouse_id' => 1],
            ['name' => 'Camiseta Básica', 'code' => 'CAM-003', 'category' => 'Camisetas', 'size' => 'L', 'color' => 'Negro', 'price' => 15.99, 'status' => 'disponible', 'warehouse_id' => 1],
            ['name' => 'Pantalón Jean', 'code' => 'PAN-001', 'category' => 'Pantalones', 'size' => '30', 'color' => 'Azul', 'price' => 45.99, 'status' => 'disponible', 'warehouse_id' => 2],
            ['name' => 'Pantalón Jean', 'code' => 'PAN-002', 'category' => 'Pantalones', 'size' => '32', 'color' => 'Azul', 'price' => 45.99, 'status' => 'disponible', 'warehouse_id' => 2],
            ['name' => 'Pantalón Jean', 'code' => 'PAN-003', 'category' => 'Pantalones', 'size' => '34', 'color' => 'Negro', 'price' => 45.99, 'status' => 'disponible', 'warehouse_id' => 2],
            ['name' => 'Vestido Casual', 'code' => 'VES-001', 'category' => 'Vestidos', 'size' => 'S', 'color' => 'Rojo', 'price' => 35.99, 'status' => 'disponible', 'warehouse_id' => 3],
            ['name' => 'Vestido Casual', 'code' => 'VES-002', 'category' => 'Vestidos', 'size' => 'M', 'color' => 'Azul', 'price' => 35.99, 'status' => 'disponible', 'warehouse_id' => 3],
            ['name' => 'Chaqueta Deportiva', 'code' => 'CHA-001', 'category' => 'Chaquetas', 'size' => 'M', 'color' => 'Gris', 'price' => 65.99, 'status' => 'disponible', 'warehouse_id' => 4],
            ['name' => 'Chaqueta Deportiva', 'code' => 'CHA-002', 'category' => 'Chaquetas', 'size' => 'L', 'color' => 'Negro', 'price' => 65.99, 'status' => 'disponible', 'warehouse_id' => 4],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}