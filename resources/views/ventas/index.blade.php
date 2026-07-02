@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="m-0 text-secondary fw-bold"><i class="fas fa-file-invoice me-2 text-success"></i>Comprobantes de Venta</h3>
        <div class="d-flex align-items-center">
            <a href="{{ route('ventas.create') }}" class="btn btn-success me-3">
                <i class="fas fa-plus me-1"></i>Nueva venta
            </a>
            <span class="fw-bold text-dark me-2">
                <i class="fas fa-file-download me-1 text-secondary"></i>Descargar tabla:
            </span>
            <div class="btn-group" role="group">
                <a href="{{ route('ventas.pdf') }}" class="btn text-white" style="background-color:#e40011;">
                    <i class="fas fa-file-pdf"></i>
                </a>
                <a href="{{ route('ventas.excel') }}" class="btn text-white" style="background-color:#49ba47;">
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
            <div class="col-md-3">
                <label class="form-label fw-bold text-muted">Buscar por serie/número</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="searchSerie" class="form-control" placeholder="Ej: NV01-000001">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold text-muted">Cliente</label>
                <input type="text" id="searchCliente" class="form-control" placeholder="Nombre del cliente">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold text-muted">Estado</label>
                <select id="searchEstado" class="form-select">
                    <option value="">Todos</option>
                    <option value="COMPLETADO">Completados</option>
                    <option value="ANULADO">Anulados</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold text-muted">Fecha desde</label>
                <input type="date" id="searchFechaDesde" class="form-control">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-outline-secondary w-100" onclick="limpiarFiltros()">
                    <i class="fas fa-times me-1"></i>Limpiar
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small" id="tablaVentas">
                <thead class="table-light text-secondary">
                    <tr>
                        <th>Fecha</th>
                        <th>Serie-Número</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th>Método Pago</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                    @php
                        $metodoPago = $sale->payments->first()?->paymentMethod?->name ?? 'N/A';
                        $fullName = trim(($sale->client->name ?? '') . ' ' . ($sale->client->last_name ?? '')) ?: 'Sin cliente';
                    @endphp
                    <tr data-serie="{{ strtolower($sale->serie . '-' . $sale->number) }}"
                        data-cliente="{{ strtolower($fullName) }}"
                        data-estado="{{ $sale->status }}"
                        data-fecha="{{ $sale->created_at ? $sale->created_at->format('Y-m-d') : '' }}">
                        <td>{{ $sale->created_at ? $sale->created_at->format('d/m/Y H:i') : '-' }}</td>
                        <td><span class="badge bg-light text-dark border fw-bold">{{ $sale->serie }}-{{ $sale->number }}</span></td>
                        <td class="fw-bold text-dark">{{ $fullName }}</td>
                        <td>{{ $sale->user->name ?? 'N/A' }}</td>
                        <td><span class="badge bg-info text-white">{{ $metodoPago }}</span></td>
                        <td class="fw-bold text-success">S/. {{ number_format($sale->total, 2) }}</td>
                        <td>
                            @if($sale->status === 'COMPLETADO' || $sale->status === 'active')
                                <span class="badge bg-success text-white">Completado</span>
                            @elseif($sale->status === 'ANULADO' || $sale->status === 'voided')
                                <span class="badge bg-danger text-white">Anulado</span>
                            @else
                                <span class="badge bg-secondary">{{ $sale->status }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('ventas.show', $sale) }}" class="btn btn-outline-info" title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($sale->status === 'COMPLETADO' || $sale->status === 'active')
                                    <form action="{{ route('ventas.anular', $sale) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de anular esta venta? El stock se restaurará.')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger" title="Anular">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('ventas.duplicate', $sale) }}" class="btn btn-outline-warning" title="Duplicar venta">
                                        <i class="fas fa-copy"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No hay ventas registradas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div id="sinResultados" class="text-center py-4 text-muted d-none">
                <i class="fas fa-search fa-2x mb-2 d-block"></i>
                No se encontraron ventas con esos filtros.
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchSerie = document.getElementById('searchSerie');
    const searchCliente = document.getElementById('searchCliente');
    const searchEstado = document.getElementById('searchEstado');
    const searchFechaDesde = document.getElementById('searchFechaDesde');
    const tbody = document.querySelector('#tablaVentas tbody');
    const sinResultados = document.getElementById('sinResultados');

    function filtrar() {
        const serie = searchSerie.value.toLowerCase().trim();
        const cliente = searchCliente.value.toLowerCase().trim();
        const estado = searchEstado.value;
        const fechaDesde = searchFechaDesde.value;
        let visibles = 0;

        tbody.querySelectorAll('tr[data-serie]').forEach(tr => {
            const s = tr.dataset.serie;
            const c = tr.dataset.cliente;
            const e = tr.dataset.estado;
            const f = tr.dataset.fecha;

            let mostrar = true;
            if (serie && !s.includes(serie)) mostrar = false;
            if (cliente && !c.includes(cliente)) mostrar = false;
            if (estado && e !== estado) mostrar = false;
            if (fechaDesde && f < fechaDesde) mostrar = false;

            tr.style.display = mostrar ? '' : 'none';
            if (mostrar) visibles++;
        });

        sinResultados.classList.toggle('d-none', visibles > 0);
    }

    searchSerie.addEventListener('input', filtrar);
    searchCliente.addEventListener('input', filtrar);
    searchEstado.addEventListener('change', filtrar);
    searchFechaDesde.addEventListener('change', filtrar);
});

function limpiarFiltros() {
    document.getElementById('searchSerie').value = '';
    document.getElementById('searchCliente').value = '';
    document.getElementById('searchEstado').value = '';
    document.getElementById('searchFechaDesde').value = '';
    document.getElementById('searchSerie').dispatchEvent(new Event('input'));
}
</script>
@endsection
