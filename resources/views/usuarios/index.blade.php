@extends('layouts.app')
@section('title', 'Usuarios')
@section('page-title', 'Gestión de Usuarios')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Nuevo Usuario
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario['id'] }}</td>
                    <td>{{ $usuario['name'] }}</td>
                    <td>{{ $usuario['email'] }}</td>
                    <td>
                        @php
                            $badge = match($usuario['role']) {
                                'administrador' => 'danger',
                                'vendedor'      => 'primary',
                                'cajero'        => 'success',
                                default         => 'secondary'
                            };
                        @endphp
                        <span class="badge bg-{{ $badge }}">{{ ucfirst($usuario['role']) }}</span>
                    </td>
                    <td>
                        <span class="badge bg-{{ $usuario['active'] ? 'success' : 'secondary' }}">
                            {{ $usuario['active'] ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('usuarios.edit', $usuario['id']) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @if($usuario['id'] !== session('user.id'))
                        <form method="POST" action="{{ route('usuarios.destroy', $usuario['id']) }}" class="d-inline"
                              onsubmit="return confirm('¿Eliminar este usuario?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">No hay usuarios registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection