<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tienda Ropa')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar {
            min-height: 100vh;
            background: #212529;
            width: 250px;
            position: fixed;
            top: 0; left: 0;
        }
        .sidebar .nav-link {
            color: #adb5bd;
            padding: 10px 20px;
            border-radius: 6px;
            margin: 2px 10px;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background: #0d6efd;
        }
        .sidebar .nav-link i { margin-right: 8px; }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .navbar-top {
            background: #fff;
            border-bottom: 1px solid #dee2e6;
            padding: 12px 20px;
            margin-left: 250px;
        }
        .brand-logo {
            padding: 20px;
            border-bottom: 1px solid #343a40;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

{{-- Sidebar --}}
<div class="sidebar d-flex flex-column">
    <div class="brand-logo">
        <h5 class="text-white mb-0">
            <i class="bi bi-shop me-2"></i>Tienda Ropa
        </h5>
        <small class="text-muted">{{ ucfirst(session('role')) }}</small>
    </div>

    <nav class="flex-grow-1 mt-2">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}"
                   class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>

            @if(session('role') === 'administrador')
            <li class="nav-item">
                <a href="{{ route('productos.index') }}"
                   class="nav-link {{ request()->routeIs('productos.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i> Productos
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('usuarios.index') }}"
                   class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Usuarios
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('bodegas.index') }}"
                   class="nav-link {{ request()->routeIs('bodegas.*') ? 'active' : '' }}">
                    <i class="bi bi-building"></i> Bodegas
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('reportes.index') }}"
                   class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart"></i> Reportes
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('historial.index') }}"
                   class="nav-link {{ request()->routeIs('historial.*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i> Historial
                </a>
            </li>
            @endif

            @if(in_array(session('role'), ['administrador', 'vendedor']))
            <li class="nav-item">
                <a href="{{ route('ordenes.index') }}"
                   class="nav-link {{ request()->routeIs('ordenes.*') ? 'active' : '' }}">
                    <i class="bi bi-cart3"></i> Órdenes
                </a>
            </li>
            @endif

            @if(in_array(session('role'), ['cajero', 'administrador']))
            <li class="nav-item">
                <a href="{{ route('ventas.index') }}"
                   class="nav-link {{ request()->routeIs('ventas.*') ? 'active' : '' }}">
                    <i class="bi bi-cash-coin"></i> Ventas
                </a>
            </li>
            @endif
        </ul>
    </nav>

    <div class="p-3 border-top border-secondary">
        <div class="text-muted small mb-2">
            <i class="bi bi-person-circle me-1"></i>
            {{ session('user.name') }}
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                <i class="bi bi-box-arrow-right me-1"></i> Cerrar sesión
            </button>
        </form>
    </div>
</div>

{{-- Navbar top --}}
<div class="navbar-top">
    <h6 class="mb-0">@yield('page-title', 'Dashboard')</h6>
</div>

{{-- Contenido principal --}}
<div class="main-content mt-3">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>