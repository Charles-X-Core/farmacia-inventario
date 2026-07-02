@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="m-0 text-secondary fw-bold">
            <i class="fas fa-file-invoice me-2 text-success"></i>
            Detalle de Venta {{ $sale->serie }}-{{ $sale->number }}
        </h3>
        <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Volver
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2 shadow-sm"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger py-2 shadow-sm">
            <i class="fas fa-exclamation-circle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-box me-2 text-primary"></i>Productos</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light text-secondary">
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Subtotal</th>
                                    <th>Lotes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sale->details as $detail)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $detail->product->name ?? 'N/A' }} <span class="text-muted">({{ $detail->product->code ?? '' }})</span></td>
                                    <td>{{ $detail->quantity }}</td>
                                    <td>S/. {{ number_format($detail->price, 2) }}</td>
                                    <td class="fw-bold text-success">S/. {{ number_format($detail->subtotal, 2) }}</td>
                                    <td>
                                        @if($detail->allotments->count() > 0)
                                            @foreach($detail->allotments as $sda)
                                                <span class="badge bg-light text-dark border">{{ $sda->allotment->code ?? 'Lote' }} ({{ $sda->quantity }})</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted">Sin detalles.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-info-circle me-2 text-info"></i>Información</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless small mb-0">
                        <tr><td class="text-muted">Estado</td><td class="text-end">
                            @if($sale->status === 'COMPLETADO' || $sale->status === 'active')
                                <span class="badge bg-success">Completado</span>
                            @elseif($sale->status === 'ANULADO' || $sale->status === 'voided')
                                <span class="badge bg-danger">Anulado</span>
                            @else
                                <span class="badge bg-secondary">{{ $sale->status }}</span>
                            @endif
                        </td></tr>
                        <tr><td class="text-muted">Fecha</td><td class="text-end">{{ $sale->created_at ? $sale->created_at->format('d/m/Y H:i') : '-' }}</td></tr>
                        <tr><td class="text-muted">Serie</td><td class="text-end fw-bold">{{ $sale->serie }}-{{ $sale->number }}</td></tr>
                        <tr><td class="text-muted">Cliente</td><td class="text-end">{{ trim(($sale->client->name ?? '') . ' ' . ($sale->client->last_name ?? '')) ?: 'Sin cliente' }}</td></tr>
                        <tr><td class="text-muted">Vendedor</td><td class="text-end">{{ $sale->user->name ?? 'N/A' }}</td></tr>
                        <tr><td class="text-muted">Almacén</td><td class="text-end">{{ $sale->warehouse->name ?? 'N/A' }}</td></tr>
                    </table>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-credit-card me-2 text-warning"></i>Pagos</h5>
                </div>
                <div class="card-body">
                    @forelse($sale->payments as $payment)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-info text-white">{{ $payment->paymentMethod->name ?? 'N/A' }}</span>
                            <span class="fw-bold text-success">S/. {{ number_format($payment->amount, 2) }}</span>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">Sin pagos registrados.</p>
                    @endforelse
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Subtotal:</span>
                        <span class="fw-bold">S/. {{ number_format($sale->subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Descuento:</span>
                        <span class="fw-bold text-danger">- S/. {{ number_format($sale->discount, 2) }}</span>
                    </div>
                    <hr class="my-1">
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold fs-5">TOTAL:</span>
                        <span class="fw-bold fs-5 text-success">S/. {{ number_format($sale->total, 2) }}</span>
                    </div>
                </div>
            </div>

            @if(($sale->status === 'COMPLETADO' || $sale->status === 'active'))
            <div class="mt-3">
                <form action="{{ route('ventas.anular', $sale) }}" method="POST" onsubmit="return confirm('¿Está seguro de anular esta venta? El stock se restaurará.')">
                    @csrf
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="fas fa-ban me-1"></i>Anular Venta
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
