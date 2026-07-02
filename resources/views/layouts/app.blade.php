<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            overflow-x: hidden;
            background-color: #f8f9fa;
        }
        #sidebar-wrapper {
            min-height: 100vh;
            width: 250px;
            background-color: #2c3e50;
            transition: all 0.3s ease;
        }
        #sidebar-wrapper .sidebar-heading {
            padding: 1.5rem;
            font-size: 1.25rem;
            color: #fff;
            background-color: #1a252f;
        }
        #sidebar-wrapper .list-group-item {
            color: #ecf0f1;
            background-color: transparent;
            border: none;
            padding: 1rem 1.5rem;
        }
        #sidebar-wrapper .list-group-item:hover {
            background-color: #34495e;
            color: #3498db;
        }
        #sidebar-wrapper .list-group-item.active {
            background-color: #3498db;
            color: white;
        }
        .navbar-custom {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        }
        .modal-backdrop {
            z-index: 1040;
        }
        .modal {
            z-index: 1050;
        }
    </style>
</head>
<body>

<div class="d-flex" id="wrapper">
    <div id="sidebar-wrapper">
        <div class="sidebar-heading fw-bold text-center">
            <i class="fas fa-boxes me-2 text-info"></i>
            {{ optional(Auth::user()?->company)->name ?? 'Sistema de Inventario' }}
        </div>
        <div class="list-group list-group-flush">
            <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action {{ Request::is('dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
            <a href="{{ route('products.index') }}" class="list-group-item list-group-item-action {{ Request::is('products*') ? 'active' : '' }}">
                <i class="fas fa-boxes me-2"></i>Productos
            </a>
            <a href="{{ route('movements.index') }}" class="list-group-item list-group-item-action {{ Request::is('movements*') ? 'active' : '' }}">
                <i class="fas fa-exchange-alt me-2"></i>Movimientos
            </a>
            <a href="{{ route('allotments.index') }}" class="list-group-item list-group-item-action {{ Request::is('allotments*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt me-2"></i>Lotes
            </a>

            <a href="{{ route('ventas.index') }}" class="list-group-item list-group-item-action {{ Request::is('ventas*') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart me-2"></i>
                Ventas
            </a>

            <form action="{{ route('webLogout') }}" method="POST" class="mt-4 px-3">
                @csrf
                <button type="submit" class="btn btn-danger w-100 btn-sm">
                    <i class="fas fa-sign-out-alt me-2"></i>Salir
                </button>
            </form>

        </div>
    </div>

    <div id="page-content-wrapper" class="w-100">
        <nav class="navbar navbar-expand-lg navbar-light navbar-custom py-3 px-4">
            <div class="container-fluid p-0">
                <h4 class="m-0 fw-bold text-secondary">Panel de Control General</h4>
                <div class="ms-auto d-flex align-items-center">
                    <i class="fas fa-user-circle fa-lg me-2 text-secondary"></i>
                    <span class="fw-semibold text-muted">{{ Auth::user()->name ?? 'Administrador' }}</span>
                </div>
            </div>
        </nav>

        <div class="container-fluid p-4">
            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
