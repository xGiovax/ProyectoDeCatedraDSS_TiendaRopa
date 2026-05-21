@extends('layouts.app')
@section('title', 'Venta #' . $venta['id'])
@section('page-title', 'Detalle de Venta #' . $venta['id'])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-success text-white">
                <i class="bi bi-receipt me-1"></i> Comprobante de Venta
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong>Venta #:</strong> {{ $venta['id'] }}</p>
                        <p><strong>Orden #:</strong> {{ $venta['order']['id'] }}</p>
                        <p><strong>Cliente:</strong> {{ $venta['order']['customer_name'] }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Cajero:</strong> {{ $venta['cashier']['name'] ?? '-' }}</p>
                        <p><strong>Método:</strong> {{ ucfirst($venta['payment_method']) }}</p>
                        <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($venta['paid_at'])->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                <table class="table">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Talla</th>
                            <th>Color</th>
                            <th>Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($venta['order']['items'] as $item)
                        <tr>
                            <td><code>{{ $item['product']['code'] }}</code></td>
                            <td>{{ $item['product']['name'] }}</td>
                            <td>{{ $item['product']['size'] }}</td>
                            <td>{{ $item['product']['color'] }}</td>
                            <td>${{ number_format($item['unit_price'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Total:</td>
                            <td class="fw-bold">${{ number_format($venta['total'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="card-footer">
                <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>
</div>
@endsection