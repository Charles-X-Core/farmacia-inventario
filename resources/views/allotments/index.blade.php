@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="m-0 text-secondary fw-bold"><i class="fas fa-boxes me-2 text-primary"></i>Control de Lotes</h3>
        <div class="d-flex align-items-center">
            <span class="fw-bold text-dark me-2">
                <i class="fas fa-file-download me-1 text-secondary"></i>Descargar tabla:
            </span>
            <div class="btn-group" role="group">
                <a href="{{ route('allotments.pdf') }}" class="btn text-white" style="background-color:#e40011;">
                    <i class="fas fa-file-pdf"></i>
                </a>
                <a href="{{ route('allotments.excel') }}" class="btn text-white" style="background-color:#49ba47;">
                    <i class="fas fa-file-excel"></i>
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2 shadow-sm"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm p-3">
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label fw-bold text-muted">Buscar por código de lote</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="searchCode" class="form-control" placeholder="Ej: LOTE-BIS-001">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold text-muted">Producto</label>
                <select id="searchProduct" class="form-select">
                    <option value="">Todos los productos</option>
                    @php
                        $productosUnicos = collect($allotments)->pluck('product_name')->filter()->unique()->sort()->values();
                    @endphp
                    @foreach($productosUnicos as $prod)
                        <option value="{{ $prod }}">{{ $prod }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold text-muted">Estado</label>
                <select id="searchEstado" class="form-select">
                    <option value="">Todos</option>
                    <option value="vigente">Vigentes</option>
                    <option value="vencido">Vencidos</option>
                    <option value="sin_stock">Sin stock</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-outline-secondary w-100" onclick="limpiarFiltros()">
                    <i class="fas fa-times me-1"></i>Limpiar
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small" id="tablaLotes">
                <thead class="table-light text-secondary">
                    <tr>
                        <th>F. Ingreso</th>
                        <th>Doc. Serie-Corr.</th>
                        <th>Cód. Lote</th>
                        <th>Producto</th>
                        <th>Cód. Barras</th>
                        <th>F. Vencimiento</th>
                        <th>Días Restantes</th>
                        <th>Stock Act.</th>
                        <th>Despachado</th>
                        <th>Disponible</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allotments as $lote)
                    @php
                        $dias = null;
                        if(!empty($lote->expiration_date)) {
                            $fechaVenc = \Carbon\Carbon::parse($lote->expiration_date)->startOfDay();
                            $fechaHoy = \Carbon\Carbon::now()->startOfDay();
                            $dias = (int) $fechaHoy->diffInDays($fechaVenc, false);
                        }
                        $disponible = ($lote->stock_actual ?? 0) - ($lote->despachado ?? 0);
                    @endphp
                    <tr data-code="{{ strtolower($lote->code) }}"
                        data-producto="{{ strtolower($lote->product_name ?? '') }}"
                        data-dias="{{ $dias }}"
                        data-disponible="{{ $disponible }}">
                        <td>{{ $lote->fecha_ingreso ? \Carbon\Carbon::parse($lote->fecha_ingreso)->format('d/m/Y') : 'F. Inicial' }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $lote->serie ?? 'LOTE' }}-{{ $lote->correlativo ?? '0000' }}</span></td>
                        <td class="fw-bold text-primary">{{ $lote->code }}</td>
                        <td class="fw-bold text-dark">{{ $lote->product_name ?? 'Sin producto' }}</td>
                        <td class="text-muted">{{ $lote->barcode ?? 'S/B' }}</td>
                        <td>{{ $lote->expiration_date ? \Carbon\Carbon::parse($lote->expiration_date)->format('d/m/Y') : 'No perecible' }}</td>
                        <td>
                            @if(is_null($dias))
                                <span class="badge bg-secondary">N/A</span>
                            @else
                                <span class="fw-bold {{ $dias <= 0 ? 'text-danger' : ($dias <= 30 ? 'text-warning' : 'text-success') }}">
                                    {{ $dias }} {{ $dias == 1 ? 'día' : 'días' }}
                                </span>
                            @endif
                        </td>
                        <td><span class="badge bg-info text-white">{{ $lote->stock_actual ?? 0 }} u.</span></td>
                        <td><span class="text-muted">{{ $lote->despachado ?? 0 }} u.</span></td>
                        <td>
                            @if($disponible > 0)
                                <span class="badge bg-success text-white">{{ $disponible }} u.</span>
                            @else
                                <span class="badge bg-danger text-white">Agotado</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-4 text-muted">No hay lotes registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div id="sinResultados" class="text-center py-4 text-muted d-none">
                <i class="fas fa-search fa-2x mb-2 d-block"></i>
                No se encontraron lotes con esos filtros.
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchCode = document.getElementById('searchCode');
    const searchProduct = document.getElementById('searchProduct');
    const searchEstado = document.getElementById('searchEstado');
    const tbody = document.querySelector('#tablaLotes tbody');
    const sinResultados = document.getElementById('sinResultados');

    function filtrar() {
        const codigo = searchCode.value.toLowerCase().trim();
        const producto = searchProduct.value.toLowerCase().trim();
        const estado = searchEstado.value;
        let visibles = 0;

        tbody.querySelectorAll('tr[data-code]').forEach(tr => {
            const code = tr.dataset.code;
            const prod = tr.dataset.producto;
            const dias = parseInt(tr.dataset.dias);
            const disponible = parseInt(tr.dataset.disponible);

            let mostrar = true;

            if (codigo && !code.includes(codigo)) mostrar = false;
            if (producto && prod !== producto) mostrar = false;

            if (estado === 'vigente' && (dias !== null && dias <= 0)) mostrar = false;
            if (estado === 'vencido' && (dias === null || dias > 0)) mostrar = false;
            if (estado === 'sin_stock' && disponible > 0) mostrar = false;

            tr.style.display = mostrar ? '' : 'none';
            if (mostrar) visibles++;
        });

        sinResultados.classList.toggle('d-none', visibles > 0);
    }

    searchCode.addEventListener('input', filtrar);
    searchProduct.addEventListener('change', filtrar);
    searchEstado.addEventListener('change', filtrar);
});

function limpiarFiltros() {
    document.getElementById('searchCode').value = '';
    document.getElementById('searchProduct').value = '';
    document.getElementById('searchEstado').value = '';
    document.getElementById('searchCode').dispatchEvent(new Event('input'));
}
</script>
@endsection
