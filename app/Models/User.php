<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'active'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'active'            => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'cashier_id');
    }

    public function history()
    {
        return $this->hasMany(History::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'administrador';
    }

    public function isVendedor(): bool
    {
        return $this->role === 'vendedor';
    }

    public function isCajero(): bool
    {
        return $this->role === 'cajero';
    }
}