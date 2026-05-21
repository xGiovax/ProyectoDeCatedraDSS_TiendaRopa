@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

@if(session('role') === 'administrador')
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                    <i class="bi bi-cash-coin text-primary fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Ventas</div>
                    <div class="fs-4 fw-bold">{{ $stats['total_ventas'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                    <i class="bi bi-currency-dollar text-success fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Ingresos Totales</div>
                    <div class="fs-4 fw-bold">${{ number_format($stats['ingreso_total'] ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                    <i class="bi bi-box-seam text-warning fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Productos Vendidos</div>
                    <div class="fs-4 fw-bold">{{ $stats['productos_vendidos'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                    <i class="bi bi-archive text-info fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Disponibles</div>
                    <div class="fs-4 fw-bold">{{ $stats['productos_disponibles'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row g-4">
    @if(in_array(session('role'), ['administrador', 'vendedor']))
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-4">
                <i class="bi bi-cart-plus text-primary fs-1 mb-3"></i>
                <h5>Nueva Orden</h5>
                <p class="text-muted">Crear una orden para un cliente</p>
                <a href="{{ route('ordenes.create') }}" class="btn btn-primary">
                    Crear Orden
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-4">
                <i class="bi bi-list-check text-success fs-1 mb-3"></i>
                <h5>Ver Órdenes</h5>
                <p class="text-muted">Gestionar órdenes activas</p>
                <a href="{{ route('ordenes.index') }}" class="btn btn-success">
                    Ver Órdenes
                </a>
            </div>
        </div>
    </div>
    @endif

    @if(in_array(session('role'), ['cajero', 'administrador']))
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-4">
                <i class="bi bi-cash-register text-warning fs-1 mb-3"></i>
                <h5>Procesar Pagos</h5>
                <p class="text-muted">Ver órdenes listas para pagar</p>
                <a href="{{ route('ventas.index') }}" class="btn btn-warning">
                    Ir a Caja
                </a>
            </div>
        </div>
    </div>
    @endif
</div>

@endsection