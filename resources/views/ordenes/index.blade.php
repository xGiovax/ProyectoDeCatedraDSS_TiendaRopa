@extends('layouts.app')
@section('title', 'Órdenes')
@section('page-title', 'Gestión de Órdenes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    @if(in_array(session('role'), ['vendedor', 'administrador']))
    <a href="{{ route('ordenes.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Nueva Orden
    </a>
    @else
    <div></div>
    @endif

    <form class="d-flex gap-2" method="GET">
        <select name="status" class="form-select" style="width:200px">
            <option value="">Todos los estados</option>
            <option value="pendiente"       {{ request('status') === 'pendiente'       ? 'selected' : '' }}>Pendiente</option>
            <option value="en_proceso"      {{ request('status') === 'en_proceso'      ? 'selected' : '' }}>En Proceso</option>
            <option value="enviada_a_caja"  {{ request('status') === 'enviada_a_caja'  ? 'selected' : '' }}>Enviada a Caja</option>
            <option value="pagada"          {{ request('status') === 'pagada'          ? 'selected' : '' }}>Pagada</option>
            <option value="cancelada"       {{ request('status') === 'cancelada'       ? 'selected' : '' }}>Cancelada</option>
        </select>
        <button type="submit" class="btn btn-secondary"><i class="bi bi-search"></i></button>
    </form>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Vendedor</th>
                    <th>Estado</th>
                    <th>Productos</th>
                    <th>Total</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ordenes as $orden)
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
                <tr>
                    <td>#{{ $orden['id'] }}</td>
                    <td>{{ $orden['customer_name'] }}</td>
                    <td>{{ $orden['seller']['name'] ?? '-' }}</td>
                    <td><span class="badge bg-{{ $badge }}">{{ ucfirst(str_replace('_', ' ', $orden['status'])) }}</span></td>
                    <td>{{ count($orden['items']) }}</td>
                    <td>${{ number_format($total, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($orden['created_at'])->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('ordenes.show', $orden['id']) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                        @if(in_array($orden['status'], ['pendiente', 'en_proceso']))
                        <form method="POST" action="{{ route('ordenes.cancel', $orden['id']) }}" class="d-inline"
                              onsubmit="return confirm('¿Cancelar esta orden?')">
                            @csrf
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-circle"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No hay órdenes registradas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection