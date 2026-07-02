@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 border-start border-primary border-4 p-2">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Productos</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $totalProductos ?? 0 }}</div>
                    </div>
                    <i class="fas fa-capsules fa-2x text-light bg-primary p-3 rounded-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 border-start border-danger border-4 p-2">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Alertas Stock Mínimo</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $stockBajo ?? 0 }}</div>
                    </div>
                    <i class="fas fa-exclamation-triangle fa-2x text-light bg-danger p-3 rounded-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 border-start border-warning border-4 p-2">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Próximos a Vencer</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $porVencer ?? 0 }}</div>
                    </div>
                    <i class="fas fa-calendar-times fa-2x text-light bg-warning p-3 rounded-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="m-0 font-weight-bold text-secondary"><i class="fas fa-history me-2 text-info"></i>Últimos Movimientos de Bodega</h5>
        </div>
        <div class="card-body text-center py-5">
            <i class="fas fa-folder-open text-muted mb-3" style="font-size: 4rem; opacity: 0.4;"></i>
            <h4 class="text-secondary fw-semibold">No hay información registrada aún</h4>
            <p class="text-muted mb-4 fs-6 mx-auto" style="max-width: 500px;">
                El sistema de PharmaTrack se ha inicializado correctamente. Los KPIs y los gráficos comenzarán a mostrar estadísticas en tiempo real una vez que se registren los primeros ingresos de productos al almacén.
            </p>
            <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm px-4 shadow-sm">
                <i class="fas fa-plus me-2"></i>Registrar Primer Producto
            </a>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="m-0 text-secondary fw-bold">
            <i class="fas fa-shopping-cart me-2 text-success"></i>
            Ventas
        </h3>
    </div>

    <div class="card border-0 shadow-sm p-3">
        <div class="text-center py-5">
            <i class="fas fa-cash-register text-muted mb-3" style="font-size: 4rem; opacity: 0.4;"></i>
            <h4 class="text-secondary">Módulo de Ventas listo</h4>
            <p class="text-muted">Aquí se mostrarán las ventas del sistema.</p>
        </div>
    </div>



</div>
@endsection
