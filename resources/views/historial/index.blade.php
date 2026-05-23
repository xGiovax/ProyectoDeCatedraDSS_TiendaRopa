@extends('layouts.app')
@section('title', 'Historial')
@section('page-title', 'Historial de Movimientos')

@section('content')
<div class="d-flex justify-content-end mb-4">
    <form class="d-flex gap-2" method="GET">
        <select name="action" class="form-select" style="width:180px">
            <option value="">Todas las acciones</option>
            <option value="reservado"  {{ request('action') === 'reservado'  ? 'selected' : '' }}>Reservado</option>
            <option value="vendido"    {{ request('action') === 'vendido'    ? 'selected' : '' }}>Vendido</option>
            <option value="cancelado"  {{ request('action') === 'cancelado'  ? 'selected' : '' }}>Cancelado</option>
            <option value="liberado"   {{ request('action') === 'liberado'   ? 'selected' : '' }}>Liberado</option>
        </select>
        <button type="submit" class="btn btn-secondary"><i class="bi bi-search"></i></button>
        <a href="{{ route('historial.index') }}" class="btn btn-outline-secondary">Limpiar</a>
    </form>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Producto</th>
                    <th>Acción</th>
                    <th>Estado anterior</th>
                    <th>Estado nuevo</th>
                    <th>Usuario</th>
                    <th>Notas</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($historial as $item)
                @php
                    $badge = match($item['action']) {
                        'reservado' => 'warning',
                        'vendido'   => 'success',
                        'cancelado' => 'danger',
                        'liberado'  => 'info',
                        default     => 'secondary'
                    };
                @endphp
                <tr>
                    <td>{{ $item['id'] }}</td>
                    <td>
                        <code>{{ $item['product']['code'] ?? '-' }}</code>
                        <small class="d-block text-muted">{{ $item['product']['name'] ?? '-' }}</small>
                    </td>
                    <td><span class="badge bg-{{ $badge }}">{{ ucfirst($item['action']) }}</span></td>
                    <td><span class="badge bg-light text-dark">{{ $item['from_status'] ?? '-' }}</span></td>
                    <td><span class="badge bg-light text-dark">{{ $item['to_status'] ?? '-' }}</span></td>
                    <td>{{ $item['user']['name'] ?? '-' }}</td>
                    <td><small>{{ $item['notes'] ?? '-' }}</small></td>
                    <td><small>{{ \Carbon\Carbon::parse($item['created_at'])->format('d/m/Y H:i') }}</small></td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No hay movimientos registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection