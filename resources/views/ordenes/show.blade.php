@extends('layouts.app')
@section('title', 'Orden #' . $orden['id'])
@section('page-title', 'Orden #' . $orden['id'])

@section('content')
@php
    $badge = match($orden['status']) {
        'pendiente'      => 'secondary',
        'en_proceso'     => 'primary',
        'enviada_a_caja' => 'warning',
        'pagada'         => 'success',
        'cancelada'      => 'danger',
        default          => 'secondary'
    };
    $total = collect($orden['items'])->sum('unit_price');
@endphp

<div class="row g-4">
    {{-- Info de la orden --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-dark text-white">
                <i class="bi bi-info-circle me-1"></i> Información
            </div>
            <div class="card-body">
                <p><strong>Cliente:</strong> {{ $orden['customer_name'] }}</p>
                <p><strong>Vendedor:</strong> {{ $orden['seller']['name'] ?? '-' }}</p>
                <p><strong>Estado:</strong> <span class="badge bg-{{ $badge }}">{{ ucfirst(str_replace('_', ' ', $orden['status'])) }}</span></p>
                <p><strong>Total:</strong> ${{ number_format($total, 2) }}</p>
                <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($orden['created_at'])->format('d/m/Y H:i') }}</p>
                @if($orden['notes'])
                <p><strong>Notas:</strong> {{ $orden['notes'] }}</p>
                @endif
            </div>

            @if(in_array(session('role'), ['vendedor', 'administrador']))
            <div class="card-footer d-flex gap-2">
                @if($orden['status'] === 'en_proceso')
                <form method="POST" action="{{ route('ordenes.sendToCashier', $orden['id']) }}">
                    @csrf
                    <button class="btn btn-warning btn-sm">
                        <i class="bi bi-send me-1"></i> Enviar a Caja
                    </button>
                </form>
                @endif
                @if(in_array($orden['status'], ['pendiente', 'en_proceso']))
                <form method="POST" action="{{ route('ordenes.cancel', $orden['id']) }}"
                      onsubmit="return confirm('¿Cancelar esta orden?')">
                    @csrf
                    <button class="btn btn-danger btn-sm">
                        <i class="bi bi-x-circle me-1"></i> Cancelar
                    </button>
                </form>
                @endif
            </div>
            @endif
        </div>
    </div>

    {{-- Productos de la orden --}}
    <div class="col-md-8">
        {{-- Agregar producto --}}
        @if(in_array(session('role'), ['vendedor', 'administrador']) && in_array($orden['status'], ['pendiente', 'en_proceso']))
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <i class="bi bi-plus-circle me-1"></i> Agregar Producto
            </div>
            <div class="card-body">
                @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <form method="POST" action="{{ route('ordenes.addItem', $orden['id']) }}" class="d-flex gap-2">
                    @csrf
                    <input type="text" name="product_id" class="form-control"
                           placeholder="ID del producto reservado" required>
                    <button type="submit" class="btn btn-primary text-nowrap">
                        <i class="bi bi-plus"></i> Agregar
                    </button>
                </form>
                <small class="text-muted">Solo productos con estado <strong>reservado</strong> pueden agregarse.</small>
            </div>
        </div>
        @endif

        {{-- Lista de productos --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-dark text-white">
                <i class="bi bi-bag me-1"></i> Productos en la Orden
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Talla</th>
                            <th>Color</th>
                            <th>Precio</th>
                            @if(in_array($orden['status'], ['pendiente', 'en_proceso']))
                            <th></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orden['items'] as $item)
                        <tr>
                            <td><code>{{ $item['product']['code'] }}</code></td>
                            <td>{{ $item['product']['name'] }}</td>
                            <td>{{ $item['product']['size'] }}</td>
                            <td>{{ $item['product']['color'] }}</td>
                            <td>${{ number_format($item['unit_price'], 2) }}</td>
                            @if(in_array($orden['status'], ['pendiente', 'en_proceso']))
                            <td>
                                <form method="POST"
                                      action="{{ route('ordenes.removeItem', [$orden['id'], $item['id']]) }}"
                                      onsubmit="return confirm('¿Remover este producto?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">No hay productos en esta orden.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(count($orden['items']) > 0)
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Total:</td>
                            <td colspan="2" class="fw-bold">${{ number_format($total, 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('ordenes.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>
</div>
@endsection