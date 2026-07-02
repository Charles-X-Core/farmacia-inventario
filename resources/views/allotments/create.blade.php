@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4 text-secondary fw-bold"><i class="fas fa-plus-circle me-2 text-primary"></i>Registrar Entrada / Salida por Lote</h3>

    <div class="card border-0 shadow-sm p-4">
        <form action="{{ route('movements.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-muted">Seleccionar Lote Destino</label>
                    <select name="allotment_id" id="allotment_id" class="form-select bg-light" onchange="updateProducts()" required>
                        <option value="">-- Elija un Lote --</option>
                        @foreach($allotments as $lote)
                            <option value="{{ $lote->id }}">Lote: {{ $lote->code }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold text-muted">Medicamento (Filtrado por Lote)</label>
                    <select name="product_id" id="product_id" class="form-select bg-light" required disabled>
                        <option value="">-- Seleccione primero un lote --</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold text-muted">Tipo de Operación</label>
                    <select name="movement_type_id" class="form-select bg-light" required>
                        <option value="1">Ingreso de mercadería (Entrada)</option>
                        <option value="2">Salida de mercadería (Salida)</option>
                        <option value="3">Venta (Salida Automática)</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold text-muted">Cantidad de Unidades</label>
                    <input type="number" name="quantity" class="form-control bg-light" min="1" value="1" required>
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('movements.index') }}" class="btn btn-light border me-2">Volver</a>
                <button type="submit" class="btn btn-primary px-5 shadow-sm">Procesar Operación</button>
            </div>
        </form>
    </div>
</div>

<script>
// Guardamos los datos que nos mandó Laravel de forma segura en un array de JS
const relacionesLoteProducto = @json($loteProductos);

function updateProducts() {
    const loteSelect = document.getElementById('allotment_id');
    const productoSelect = document.getElementById('product_id');
    const loteIdSelected = loteSelect.value;

    // Limpiamos el selector de productos
    productoSelect.innerHTML = '<option value="">-- Seleccione un medicamento --</option>';

    if (!loteIdSelected) {
        productoSelect.disabled = true;
        return;
    }

    // Filtramos las relaciones que correspondan estrictamente al lote seleccionado
    const productosFiltrados = relacionesLoteProducto.filter(item => item.allotment_id == loteIdSelected);

    if (productosFiltrados.length === 0) {
        // BONUS: Si el lote es nuevo e independiente y no tiene productos, le permitimos asociarle cualquier producto de contingencia
        productoSelect.innerHTML = '<option value="">-- Lote Vacío: Se vinculará el producto al guardar --</option>';
        @json(\App\Models\Product::all()).forEach(p => {
            productoSelect.innerHTML += `<option value="${p.id}">${p.name} (SKU: ${p.code})</option>`;
        });
    } else {
        // Si ya tiene productos guardados en este lote, pintamos únicamente esos
        productosFiltrados.forEach(prod => {
            productoSelect.innerHTML += `<option value="${prod.product_id}">${prod.product_name}</option>`;
        });
    }

    // Activamos el selector
    productoSelect.disabled = false;
}
</script>
@endsection
