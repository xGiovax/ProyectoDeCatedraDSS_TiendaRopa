@extends('layouts.app')
@section('title', 'Productos')
@section('page-title', 'Gestión de Productos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    @if(session('role') === 'administrador')
    <a href="{{ route('productos.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Nuevo Producto
    </a>
    @else
    <div></div>
    @endif

    <form class="d-flex gap-2" method="GET">
        <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o código..." value="{{ request('search') }}">
        <select name="status" class="form-select" style="width:160px">
            <option value="">Todos los estados</option>
            <option value="disponible" {{ request('status') === 'disponible' ? 'selected' : '' }}>Disponible</option>
            <option value="reservado"  {{ request('status') === 'reservado'  ? 'selected' : '' }}>Reservado</option>
            <option value="vendido"    {{ request('status') === 'vendido'    ? 'selected' : '' }}>Vendido</option>
        </select>
        <button type="submit" class="btn btn-secondary">
            <i class="bi bi-search"></i>
        </button>
    </form>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Talla</th>
                    <th>Color</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th>Ubicación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productos as $producto)
                <tr>
                    <td><code>{{ $producto['code'] }}</code></td>
                    <td>{{ $producto['name'] }}</td>
                    <td>{{ $producto['category'] }}</td>
                    <td>{{ $producto['size'] }}</td>
                    <td>{{ $producto['color'] }}</td>
                    <td>${{ number_format($producto['price'], 2) }}</td>
                    <td>
                        @php
                            $badge = match($producto['status']) {
                                'disponible' => 'success',
                                'reservado'  => 'warning',
                                'vendido'    => 'secondary',
                                default      => 'primary'
                            };
                        @endphp
                        <span class="badge bg-{{ $badge }}">{{ ucfirst($producto['status']) }}</span>
                    </td>
                    <td>
                        @if($producto['warehouse'])
                            Estante {{ $producto['warehouse']['shelf'] }} - Módulo {{ $producto['warehouse']['module'] }}
                        @else
                            <span class="text-muted">Sin asignar</span>
                        @endif
                    </td>
                    <td>
                        @if(session('role') === 'administrador')
                        <a href="{{ route('productos.edit', $producto['id']) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('productos.destroy', $producto['id']) }}" class="d-inline"
                              onsubmit="return confirm('¿Eliminar este producto?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endif
                        @if(in_array(session('role'), ['vendedor', 'administrador']) && $producto['status'] === 'disponible')
                        <form method="POST" action="{{ route('productos.reserve', $producto['id']) }}" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-outline-warning" title="Reservar">
                                <i class="bi bi-bookmark"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">No hay productos registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection