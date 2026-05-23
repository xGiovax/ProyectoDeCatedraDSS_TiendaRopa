@extends('layouts.app')
@section('title', 'Reportes')
@section('page-title', 'Reportes y Estadísticas')

@section('content')

{{-- Tarjetas resumen --}}
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                    <i class="bi bi-cart-check text-primary fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Ventas</div>
                    <div class="fs-4 fw-bold">{{ $dashboard['total_ventas'] ?? 0 }}</div>
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
                    <div class="fs-4 fw-bold">${{ number_format($dashboard['ingreso_total'] ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                    <i class="bi bi-bag-check text-warning fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Productos Vendidos</div>
                    <div class="fs-4 fw-bold">{{ $dashboard['productos_vendidos'] ?? 0 }}</div>
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
                    <div class="fs-4 fw-bold">{{ $dashboard['productos_disponibles'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Ventas diarias --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-dark text-white">
                <i class="bi bi-calendar-check me-1"></i> Ventas Diarias
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Ventas</th>
                            <th>Ingresos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventasDiarias as $dia)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($dia['fecha'])->format('d/m/Y') }}</td>
                            <td>{{ $dia['total_ventas'] }}</td>
                            <td>${{ number_format($dia['ingresos'], 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-3">Sin datos.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Inventario --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-dark text-white">
                <i class="bi bi-pie-chart me-1"></i> Estado del Inventario
            </div>
            <div class="card-body">
                @php
                    $total = ($inventario['disponibles'] ?? 0) + ($inventario['reservados'] ?? 0) + ($inventario['vendidos'] ?? 0);
                @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Disponibles</span>
                        <strong>{{ $inventario['disponibles'] ?? 0 }}</strong>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-success" style="width: {{ $total > 0 ? ($inventario['disponibles']/$total)*100 : 0 }}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Reservados</span>
                        <strong>{{ $inventario['reservados'] ?? 0 }}</strong>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-warning" style="width: {{ $total > 0 ? ($inventario['reservados']/$total)*100 : 0 }}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Vendidos</span>
                        <strong>{{ $inventario['vendidos'] ?? 0 }}</strong>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-secondary" style="width: {{ $total > 0 ? ($inventario['vendidos']/$total)*100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Productos más vendidos --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-dark text-white">
                <i class="bi bi-trophy me-1"></i> Productos Más Vendidos
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Veces Vendido</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($masVendidos as $i => $item)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td><code>{{ $item['product']['code'] ?? '-' }}</code></td>
                            <td>{{ $item['product']['name'] ?? '-' }}</td>
                            <td>{{ $item['product']['category'] ?? '-' }}</td>
                            <td><span class="badge bg-primary">{{ $item['total_vendido'] }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">Sin datos de ventas aún.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection