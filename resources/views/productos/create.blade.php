@extends('layouts.app')
@section('title', 'Nuevo Producto')
@section('page-title', 'Nuevo Producto')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">

                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif

                <form method="POST" action="{{ route('productos.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Código único</label>
                            <input type="text" name="code" class="form-control" value="{{ old('code') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Categoría</label>
                            <input type="text" name="category" class="form-control" value="{{ old('category') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Talla</label>
                            <input type="text" name="size" class="form-control" value="{{ old('size') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Color</label>
                            <input type="text" name="color" class="form-control" value="{{ old('color') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Precio</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="price" step="0.01" min="0" class="form-control" value="{{ old('price') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ubicación en bodega</label>
                            <select name="warehouse_id" class="form-select">
                                <option value="">Sin asignar</option>
                                @foreach($bodegas as $bodega)
                                <option value="{{ $bodega['id'] }}" {{ old('warehouse_id') == $bodega['id'] ? 'selected' : '' }}>
                                    Estante {{ $bodega['shelf'] }} - Módulo {{ $bodega['module'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Descripción</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Guardar Producto
                        </button>
                        <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection