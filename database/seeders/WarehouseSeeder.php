<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warehouse;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['shelf' => 'A', 'module' => '1', 'description' => 'Estante A - Módulo 1'],
            ['shelf' => 'A', 'module' => '2', 'description' => 'Estante A - Módulo 2'],
            ['shelf' => 'A', 'module' => '3', 'description' => 'Estante A - Módulo 3'],
            ['shelf' => 'B', 'module' => '1', 'description' => 'Estante B - Módulo 1'],
            ['shelf' => 'B', 'module' => '2', 'description' => 'Estante B - Módulo 2'],
            ['shelf' => 'B', 'module' => '3', 'description' => 'Estante B - Módulo 3'],
            ['shelf' => 'C', 'module' => '1', 'description' => 'Estante C - Módulo 1'],
            ['shelf' => 'C', 'module' => '2', 'description' => 'Estante C - Módulo 2'],
        ];

        foreach ($locations as $location) {
            Warehouse::create($location);
        }
    }
}