@extends('layouts.app')
@section('title', 'Bodegas')
@section('page-title', 'Gestión de Bodegas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('bodegas.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Nueva Ubicación
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Estante</th>
                    <th>Módulo</th>
                    <th>Descripción</th>
                    <th>Productos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bodegas as $bodega)
                <tr>
                    <td>{{ $bodega['id'] }}</td>
                    <td><span class="badge bg-dark fs-6">{{ $bodega['shelf'] }}</span></td>
                    <td><span class="badge bg-secondary fs-6">{{ $bodega['module'] }}</span></td>
                    <td>{{ $bodega['description'] ?? '-' }}</td>
                    <td>
                        <span class="badge bg-info text-dark">
                            {{ count($bodega['products']) }} productos
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('bodegas.edit', $bodega['id']) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('bodegas.destroy', $bodega['id']) }}" class="d-inline"
                              onsubmit="return confirm('¿Eliminar esta ubicación?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">No hay ubicaciones registradas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection