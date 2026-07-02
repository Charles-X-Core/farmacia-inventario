@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="m-0 text-secondary fw-bold"><i class="fas fa-plus-circle me-2 text-primary"></i>Registrar Nuevo Producto</h3>
        <a href="{{ route('products.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Volver al Catálogo
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ is_array($error) ? implode(', ', $error) : $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm p-4">
        <form action="{{ route('products.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-muted">Nombre del Producto / Medicamento</label>
                    <input type="text" name="name" class="form-control bg-light" placeholder="Ej. Paracetamol 500mg" value="{{ old('name') }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold text-muted">Código Único (SKU)</label>
                    <input type="text" name="code" class="form-control bg-light" placeholder="Ej. P001" value="{{ old('code') }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold text-muted">Código de Barras (Opcional)</label>
                    <input type="text" name="barcode" class="form-control bg-light" placeholder="Ej. 7750123456" value="{{ old('barcode') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold text-muted">Precio de Venta (S/.)</label>
                    <input type="number" name="price" class="form-control bg-light" step="0.01" min="0" placeholder="0.00" value="{{ old('price') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold text-muted">Precio de Costo (S/.)</label>
                    <input type="number" name="price_cost" class="form-control bg-light" step="0.01" min="0" placeholder="0.00" value="{{ old('price_cost') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold text-muted">Tipo de Gestión</label>
                    <select name="type_management_id" class="form-select bg-light" required>
                        <option value="1" {{ old('type_management_id') == 1 ? 'selected' : '' }}>General / Bodega (Libre)</option>
                        <option value="2" {{ old('type_management_id') == 2 ? 'selected' : '' }}>Lotes</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold text-muted d-block">¿Es Inventariable?</label>
                    <div class="form-check form-check-inline mt-1">
                        <input class="form-check-input" type="radio" name="is_inventoriable" id="inv_si" value="1" checked>
                        <label class="form-check-label" for="inv_si">Sí, controlar Stock</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="is_inventoriable" id="inv_no" value="0">
                        <label class="form-check-label" for="inv_no">No</label>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold text-muted d-block">¿Maneja Precio de Costo?</label>
                    <div class="form-check form-check-inline mt-1">
                        <input class="form-check-input" type="radio" name="is_price_cost" id="costo_si" value="1" checked>
                        <label class="form-check-label" for="costo_si">Sí</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="is_price_cost" id="costo_no" value="0">
                        <label class="form-check-label" for="costo_no">No</label>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold text-muted d-block">¿Habilitado para la Venta?</label>
                    <div class="form-check form-check-inline mt-1">
                        <input class="form-check-input" type="radio" name="is_sale" id="venta_si" value="1" checked>
                        <label class="form-check-label" for="venta_si">Sí, disponible</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="is_sale" id="venta_no" value="0">
                        <label class="form-check-label" for="venta_no">No</label>
                    </div>
                </div>
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-primary px-5 shadow-sm">
                    <i class="fas fa-save me-2"></i>Guardar Producto en PharmaTrack
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
