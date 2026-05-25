@extends('layouts.app')
@section('title', 'Productos')
@section('page-title', 'Gestión de Productos')

@section('content')

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Buscar</label>
                <input type="text" name="search" class="form-control"
                       placeholder="Nombre o código..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Estado</label>
                <select name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="disponible" {{ request('status') === 'disponible' ? 'selected' : '' }}>Disponible</option>
                    <option value="reservado"  {{ request('status') === 'reservado'  ? 'selected' : '' }}>Reservado</option>
                    <option value="vendido"    {{ request('status') === 'vendido'    ? 'selected' : '' }}>Vendido</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Categoría</label>
                <select name="category" class="form-select">
                    <option value="">Todas</option>
                    @foreach($categorias as $cat)
                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Talla</label>
                <select name="size" class="form-select">
                    <option value="">Todas</option>
                    @foreach($tallas as $talla)
                    <option value="{{ $talla }}" {{ request('size') === $talla ? 'selected' : '' }}>{{ $talla }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Color</label>
                <select name="color" class="form-select">
                    <option value="">Todos</option>
                    @foreach($colores as $color)
                    <option value="{{ $color }}" {{ request('color') === $color ? 'selected' : '' }}>{{ $color }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>
            
            @if(request()->hasAny(['search','status','category','size','color','shelf','module']))
            <div class="col-12">
                <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-circle me-1"></i> Limpiar filtros
                </a>
                <span class="text-muted ms-2 small">{{ count($productos) }} producto(s) encontrado(s)</span>
            </div>
            @endif
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    @if(session('role') === 'administrador')
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Lista de Productos</span>
        <a href="{{ route('productos.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Nuevo Producto
        </a>
    </div>
    @endif
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
                    <th>Stock Total</th>
                    <th>Disponible</th>
                    <th>Reservado</th>
                    <th>Vendido</th>
                    <th>Estado</th>
                    <th>Ubicación</th>
                    @if(session('role') === 'administrador')
                    <th>Acciones</th>
                    @endif
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
                    <td><span class="badge bg-dark">{{ $producto['stock'] }}</span></td>
                    <td><span class="badge bg-success">{{ $producto['stock_disponible'] }}</span></td>
                    <td><span class="badge bg-warning text-dark">{{ $producto['stock_reservado'] }}</span></td>
                    <td><span class="badge bg-secondary">{{ $producto['stock_vendido'] }}</span></td>
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
                            <span class="badge bg-light text-dark border">
                                Estante {{ $producto['warehouse']['shelf'] }} - Módulo {{ $producto['warehouse']['module'] }}
                            </span>
                        @else
                            <span class="text-muted">Sin asignar</span>
                        @endif
                    </td>
                    @if(session('role') === 'administrador')
                    <td>
                        <a href="{{ route('productos.edit', $producto['id']) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('productos.destroy', $producto['id']) }}" class="d-inline"
                              onsubmit="return confirm('¿Eliminar este producto?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="13" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                        No se encontraron productos.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection