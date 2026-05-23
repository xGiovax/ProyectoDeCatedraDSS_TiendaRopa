@extends('layouts.app')
@section('title', 'Nueva Ubicación')
@section('page-title', 'Nueva Ubicación de Bodega')

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

                <form method="POST" action="{{ route('bodegas.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Estante</label>
                        <input type="text" name="shelf" class="form-control"
                               value="{{ old('shelf') }}" required placeholder="Ej: A, B, C">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Módulo</label>
                        <input type="text" name="module" class="form-control"
                               value="{{ old('module') }}" required placeholder="Ej: 1, 2, 3">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Descripción <span class="text-muted">(opcional)</span></label>
                        <input type="text" name="description" class="form-control"
                               value="{{ old('description') }}" placeholder="Ej: Estante A - Módulo 1">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Guardar
                        </button>
                        <a href="{{ route('bodegas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection