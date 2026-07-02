@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="m-0 text-secondary fw-bold"><i class="fas fa-clipboard-list me-2 text-info"></i>Kardex de Inventario</h3>
    </div>

    <div class="card border-0 shadow-sm p-3">
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label fw-bold text-muted">Buscar producto</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="searchProduct" class="form-control" placeholder="Nombre o código..." oninput="filtrarKardex()">
                </div>
            </div>
        </div>

        @forelse($products as $product)
        <div class="card border mb-3 product-card" data-search="{{ strtolower($product->code . ' ' . $product->name) }}">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <div>
                    <span class="fw-bold text-primary">{{ $product->code }}</span>
                    <span class="fw-bold text-dark ms-2">{{ $product->name }}</span>
                </div>
                <div>
                    <span class="badge bg-success">Stock Actual: {{ $product->current_stock ?? 0 }}</span>
                    <span class="badge bg-info ms-1">Entradas: {{ $product->total_in }}</span>
                    <span class="badge bg-warning text-dark ms-1">Salidas: {{ $product->total_out }}</span>
                </div>
            </div>
            <div class="card-body p-0">
                @if($product->movements->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0 small">
                        <thead class="table-light text-secondary">
                            <tr>
                                <th>Fecha</th>
                                <th>Serie-Número</th>
                                <th>Tipo Movimiento</th>
                                <th>Entradas</th>
                                <th>Salidas</th>
                                <th>Acumulado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($product->movements as $mov)
                            <tr>
                                <td>{{ $mov->created_at ? \Carbon\Carbon::parse($mov->created_at)->format('d/m/Y H:i') : '-' }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $mov->serie }}-{{ $mov->number }}</span></td>
                                <td>
                                    @if($mov->is_entry)
                                        <span class="badge bg-success"> Entrada</span>
                                    @else
                                        <span class="badge bg-danger"> Salida</span>
                                    @endif
                                    <span class="text-muted small ms-1">{{ $mov->type_name }}</span>
                                </td>
                                <td class="text-success fw-bold">{{ $mov->quantity_in > 0 ? '+' . $mov->quantity_in : '' }}</td>
                                <td class="text-danger fw-bold">{{ $mov->quantity_out > 0 ? '-' . $mov->quantity_out : '' }}</td>
                                <td class="fw-bold">{{ $mov->accumulated }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-3 text-muted">Sin movimientos registrados.</div>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center py-5 text-muted">
            <i class="fas fa-clipboard-list fa-3x mb-3 d-block"></i>
            <p>No hay productos para mostrar en el kardex.</p>
        </div>
        @endforelse
    </div>
</div>

<script>
function filtrarKardex() {
    const term = document.getElementById('searchProduct').value.toLowerCase().trim();
    document.querySelectorAll('.product-card').forEach(card => {
        const match = card.dataset.search.includes(term);
        card.style.display = match ? '' : 'none';
    });
}
</script>
@endsection
