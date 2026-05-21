@extends('layouts.app')
@section('title', 'Nueva Orden')
@section('page-title', 'Nueva Orden')

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

                <form method="POST" action="{{ route('ordenes.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nombre del Cliente</label>
                        <input type="text" name="customer_name" class="form-control"
                               value="{{ old('customer_name') }}" required
                               placeholder="Ej: Juan Pérez">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Notas <span class="text-muted">(opcional)</span></label>
                        <textarea name="notes" class="form-control" rows="3"
                                  placeholder="Observaciones de la orden...">{{ old('notes') }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> Crear Orden
                        </button>
                        <a href="{{ route('ordenes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection