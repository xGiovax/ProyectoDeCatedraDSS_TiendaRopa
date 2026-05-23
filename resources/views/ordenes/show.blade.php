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
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-dark text-white">
                <i class="bi bi-info-circle me-1"></i> Información
            </div>
            <div class="card-body">
                <p><strong>Cliente:</strong> {{ $orden['customer_name'] }}</p>
                <p><strong>Vendedor:</strong> {{ $orden['seller']['name'] ?? '-' }}</p>
                <p><strong>Estado:</strong>
                    <span class="badge bg-{{ $badge }}">
                        {{ ucfirst(str_replace('_', ' ', $orden['status'])) }}
                    </span>
                </p>
                <p><strong>Total:</strong> ${{ number_format($total, 2) }}</p>
                <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($orden['created_at'])->format('d/m/Y H:i') }}</p>
                @if($orden['notes'])
                <p><strong>Notas:</strong> {{ $orden['notes'] }}</p>
                @endif
            </div>

            @if(in_array(session('role'), ['vendedor', 'administrador']))
            <div class="card-footer d-flex gap-2 flex-wrap">
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
                      onsubmit="return confirm('¿Cancelar esta orden? Los productos volverán a estar disponibles.')">
                    @csrf
                    <button class="btn btn-danger btn-sm">
                        <i class="bi bi-x-circle me-1"></i> Cancelar Orden
                    </button>
                </form>
                @endif
            </div>
            @endif
        </div>
    </div>

    <div class="col-md-8">
        @if(in_array(session('role'), ['vendedor', 'administrador']) && in_array($orden['status'], ['pendiente', 'en_proceso']))
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <i class="bi bi-search me-1"></i> Buscar y Agregar Producto
            </div>
            <div class="card-body">
                <div id="alertaProducto"></div>
                <input type="text" id="buscadorProducto" class="form-control mb-2"
                       placeholder="Escribe nombre o código del producto...">
                <div id="listaProductos" style="max-height:250px; overflow-y:auto; display:none;">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Talla</th>
                                <th>Color</th>
                                <th>Precio</th>
                                <th>Ubicación</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tablaProductos"></tbody>
                    </table>
                </div>
                <small class="text-muted mt-1 d-block">
                    Solo productos <strong>disponibles</strong>. Se reservan automáticamente al agregarlos.
                </small>
            </div>
        </div>
        @endif

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
                                      onsubmit="return confirm('¿Remover este producto? Volverá a estar disponible.')">
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

@section('scripts')
<script>
let timeoutBusqueda;
const ordenId = {{ $orden['id'] }};
const apiToken = document.querySelector('meta[name="api-token"]').content;
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

document.getElementById('buscadorProducto')?.addEventListener('input', function() {
    clearTimeout(timeoutBusqueda);
    const q = this.value.trim();
    if (q.length < 2) {
        document.getElementById('listaProductos').style.display = 'none';
        return;
    }
    timeoutBusqueda = setTimeout(() => buscarProductos(q), 400);
});

function buscarProductos(q) {
    fetch(`/api/products?search=${encodeURIComponent(q)}&status=disponible`, {
        headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + apiToken
        }
    })
    .then(r => r.json())
    .then(productos => {
        const tbody = document.getElementById('tablaProductos');
        const lista = document.getElementById('listaProductos');

        if (!Array.isArray(productos) || productos.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-2">No se encontraron productos disponibles.</td></tr>';
        } else {
            tbody.innerHTML = productos.map(p => `
                <tr id="fila-${p.id}">
                    <td><code>${p.code}</code></td>
                    <td>${p.name}</td>
                    <td>${p.size}</td>
                    <td>${p.color}</td>
                    <td>$${parseFloat(p.price).toFixed(2)}</td>
                    <td>${p.warehouse ? 'Estante ' + p.warehouse.shelf + ' - Módulo ' + p.warehouse.module : 'Sin asignar'}</td>
                    <td>
                        <button type="button" onclick="agregarProducto(${p.id}, this)" class="btn btn-sm btn-success">
                            <i class="bi bi-plus"></i> Agregar
                        </button>
                    </td>
                </tr>
            `).join('');
        }
        lista.style.display = 'block';
    })
    .catch(() => {
        document.getElementById('tablaProductos').innerHTML =
            '<tr><td colspan="7" class="text-center text-danger">Error al buscar productos.</td></tr>';
        document.getElementById('listaProductos').style.display = 'block';
    });
}

function agregarProducto(productId, btn) {
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    fetch(`/ordenes/${ordenId}/agregar-producto`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: `product_id=${productId}&_token=${csrfToken}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            document.getElementById('alertaProducto').innerHTML =
                `<div class="alert alert-danger alert-dismissible">
                    ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-plus"></i> Agregar';
        }
    })
    .catch(() => window.location.reload());
}
</script>
@endsection