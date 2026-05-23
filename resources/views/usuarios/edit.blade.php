@extends('layouts.app')
@section('title', 'Editar Usuario')
@section('page-title', 'Editar Usuario')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">

                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif

                <form method="POST" action="{{ route('usuarios.update', $usuario['id']) }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Nombre completo</label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name', $usuario['name']) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email', $usuario['email']) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nueva contraseña <span class="text-muted">(dejar vacío para no cambiar)</span></label>
                        <input type="password" name="password" class="form-control" minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select name="role" class="form-select" required>
                            <option value="administrador" {{ $usuario['role'] === 'administrador' ? 'selected' : '' }}>Administrador</option>
                            <option value="vendedor"      {{ $usuario['role'] === 'vendedor'      ? 'selected' : '' }}>Vendedor</option>
                            <option value="cajero"        {{ $usuario['role'] === 'cajero'        ? 'selected' : '' }}>Cajero</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Estado</label>
                        <select name="active" class="form-select">
                            <option value="1" {{ $usuario['active'] ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ !$usuario['active'] ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Actualizar Usuario
                        </button>
                        <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection