@extends('layouts.app')
@section('title', 'Ventas')
@section('page-title', 'Caja - Ventas')

@section('content')
<div class="row g-4">

    {{-- Órdenes listas para pagar --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-dark">
                <i class="bi bi-cash-register me-1"></i> Órdenes Enviadas a Caja
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Vendedor</th>
                            <th>Productos</th>
                            <th>Total</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ordenes as $orden)
                        @php $total = collect($orden['items'])->sum('unit_price'); @endphp
                        <tr>
                            <td>#{{ $orden['id'] }}</td>
                            <td>{{ $orden['customer_name'] }}</td>
                            <td>{{ $orden['seller']['name'] ?? '-' }}</td>
                            <td>{{ count($orden['items']) }}</td>
                            <td>${{ number_format($total, 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($orden['created_at'])->format('d/m/Y H:i') }}</td>
                            <td class="d-flex gap-2">
                                <button class="btn btn-sm btn-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalPago"
                                        data-orden-id="{{ $orden['id'] }}"
                                        data-cliente="{{ $orden['customer_name'] }}"
                                        data-total="{{ number_format($total, 2) }}">
                                    <i class="bi bi-credit-card me-1"></i> Cobrar
                                </button>
                                <form method="POST"
                                      action="{{ route('ventas.cancel', $orden['id']) }}"
                                      onsubmit="return confirm('¿Cancelar esta orden? Los productos volverán a estar disponibles.')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-x-circle me-1"></i> Cancelar
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No hay órdenes pendientes de pago.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Historial de ventas --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-dark text-white">
                <i class="bi bi-receipt me-1"></i> Historial de Ventas
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#Venta</th>
                            <th>#Orden</th>
                            <th>Cliente</th>
                            <th>Cajero</th>
                            <th>Total</th>
                            <th>Método</th>
                            <th>Fecha</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventas as $venta)
                        <tr>
                            <td>#{{ $venta['id'] }}</td>
                            <td>#{{ $venta['order']['id'] }}</td>
                            <td>{{ $venta['order']['customer_name'] }}</td>
                            <td>{{ $venta['cashier']['name'] ?? '-' }}</td>
                            <td>${{ number_format($venta['total'], 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $venta['payment_method'] === 'efectivo' ? 'success' : 'primary' }}">
                                    {{ ucfirst($venta['payment_method']) }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($venta['paid_at'])->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('ventas.show', $venta['id']) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No hay ventas registradas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal de pago --}}
<div class="modal fade" id="modalPago" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-cash-coin me-2"></i>Procesar Pago</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="formPago">
                @csrf
                <div class="modal-body">
                    <p><strong>Cliente:</strong> <span id="modalCliente"></span></p>
                    <p><strong>Total a cobrar:</strong> $<span id="modalTotal"></span></p>
                    <div class="mb-3">
                        <label class="form-label">Método de Pago</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method"
                                       value="efectivo" id="efectivo" checked>
                                <label class="form-check-label" for="efectivo">
                                    <i class="bi bi-cash me-1"></i> Efectivo
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method"
                                       value="tarjeta" id="tarjeta">
                                <label class="form-check-label" for="tarjeta">
                                    <i class="bi bi-credit-card me-1"></i> Tarjeta
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i> Confirmar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const modalPago = document.getElementById('modalPago');
    modalPago.addEventListener('show.bs.modal', function(e) {
        const btn     = e.relatedTarget;
        const ordenId = btn.getAttribute('data-orden-id');
        const cliente = btn.getAttribute('data-cliente');
        const total   = btn.getAttribute('data-total');

        document.getElementById('modalCliente').textContent = cliente;
        document.getElementById('modalTotal').textContent   = total;
        document.getElementById('formPago').action = '/ventas/' + ordenId + '/procesar';
    });
</script>
@endsection