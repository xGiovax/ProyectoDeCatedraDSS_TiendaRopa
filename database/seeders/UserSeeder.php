<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@tienda.com',
            'password' => Hash::make('password'),
            'role'     => 'administrador',
            'active'   => true,
        ]);

        User::create([
            'name'     => 'Vendedor Uno',
            'email'    => 'vendedor@tienda.com',
            'password' => Hash::make('password'),
            'role'     => 'vendedor',
            'active'   => true,
        ]);

        User::create([
            'name'     => 'Cajero Uno',
            'email'    => 'cajero@tienda.com',
            'password' => Hash::make('password'),
            'role'     => 'cajero',
            'active'   => true,
        ]);
    }
}