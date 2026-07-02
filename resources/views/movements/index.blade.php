@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="m-0 text-secondary fw-bold"><i class="fas fa-exchange-alt me-2 text-info"></i>Kardex / Movimientos de Almacén</h3>


        <span class="fw-bold text-dark me-2">
        <i class="fas fa-file-download me-1 text-secondary"></i>
            Descargar tabla:
        </span>

        <div class="btn-group" role="group">
            <a href="{{ route('movements.pdf') }}" class="btn text-white"
                style="background-color:#e40011;">
                <i class="fas fa-file-pdf"></i>
            </a>

            <a href="{{ route('movements.excel') }}" class="btn text-white" style="background-color:#49ba47;">
                <i class="fas fa-file-excel"></i>
            </a>
        </div>

        <a href="{{ route('movements.create') }}" class="btn btn-primary shadow-sm">Registrar Entrada / Salida</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2 shadow-sm"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary">
                    <tr>
                        <th>Fecha Registro</th>
                        <th>Serie</th>
                        <th>Correlativo</th>
                        <th>Producto / Medicamento</th>
                        <th>Tipo Operación</th>
                        <th>Cantidad Afectada</th>
                        <th>Lote Involucrado</th>
                        <th>Operaciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $mov)
                    <tr>
                        <td>{{ isset($mov->created_at) ? \Carbon\Carbon::parse($mov->created_at)->format('d/m/Y H:i') : 'Reciente' }}</td>

                        <td class="fw-bold text-primary">{{ $mov->serie ?? '-' }}</td>

                        <td class="fw-bold text-primary">{{ $mov->number ?? '-' }}</td>

                        <td class="fw-bold text-dark">{{ $mov->product_name ?? 'Producto General' }}</td>

                        <td>
                            @if(($mov->movement_type_id ?? 1) == 1)
                                <span class="badge bg-success"><i class="fas fa-arrow-down me-1"></i> Entrada / Ingreso</span>
                            @elseif(($mov->movement_type_id ?? 1) == 2)
                                <span class="badge bg-danger"><i class="fas fa-arrow-up me-1"></i> Salida / Mercadería</span>
                            @else
                                <span class="badge bg-primary"><i class="fas fa-shopping-cart me-1"></i> Venta Registrada</span>
                            @endif
                        </td>

                        <td class="fw-bold fs-6 text-secondary">{{ $mov->quantity ?? 0 }} unidades</td>

                        <td>
                            @if(isset($mov->allotment_codes) && count($mov->allotment_codes) > 0)
                                @foreach($mov->allotment_codes as $code)
                                    <span class="text-primary fw-mono fw-bold d-block">
                                        <i class="fas fa-hashtag me-1 text-muted"></i>{{ $code }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-muted fw-mono">Inventario General / Sin Lote</span>
                            @endif
                        </td>

                        <td>
                            <button
                                type="button"
                                class="btn btn-info btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#infoModal{{ $loop->index }}">
                                <i class="fas fa-eye"></i>
                                Info
                            </button>

                            <button
                                class="btn btn-danger btn-sm"
                                onclick="deleteMovement({{ $mov->id ?? $mov->movement_id ?? '' }}, this)">
                                <i class="fas fa-trash"></i>
                                Quitar
                            </button>
                        </td>
                    </tr>

                    <div class="modal fade"
                        id="infoModal{{ $loop->index }}"
                        tabindex="-1">

                        <div class="modal-dialog">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        Detalle del Movimiento
                                    </h5>

                                    <button
                                        type="button"
                                        class="btn-close"
                                        data-bs-dismiss="modal">
                                    </button>
                                </div>

                                <div class="modal-body">

                                    <p>
                                        <strong>Serie:</strong>
                                        {{ $mov->serie }}
                                    </p>

                                    <p>
                                        <strong>Correlativo:</strong>
                                        {{ $mov->number }}
                                    </p>

                                    <p>
                                        <strong>Producto:</strong>
                                        {{ $mov->product_name }}
                                    </p>

                                    <p>
                                        <strong>Cantidad:</strong>
                                        {{ $mov->quantity }}
                                    </p>

                                    <p>
                                        <strong>Fecha:</strong>
                                        {{ \Carbon\Carbon::parse($mov->created_at)->format('d/m/Y H:i') }}
                                    </p>

                                </div>

                            </div>
                        </div>
                    </div>

                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-exchange-alt d-block fa-2x mb-2 opacity-50"></i>
                            Aún no se registran movimientos en el historial.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function deleteMovement(id, button) {
    if (!confirm('¿Seguro que deseas eliminar este movimiento?')) return;

    fetch(`/movements/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
    console.log(data);

    if (data.success) {
        button.closest('tr').remove();
    } else {
        alert('No se pudo eliminar');
    }
    });
}
</script>
@endsection
