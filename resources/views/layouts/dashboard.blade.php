

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Inicio') | JENALEX DYNAMICS</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f7fb;
            color: #1e293b;
        }

        .dashboard-layout {
            display: flex;
            min-height: 100vh;
        }

        /* MENÚ LATERAL */

        .sidebar {
            width: 260px;
            min-width: 260px;
            background: #172554;
            color: white;
            display: flex;
            flex-direction: column;
            padding: 25px 18px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 5px 28px;
            border-bottom: 1px solid rgba(255,255,255,.15);
        }

        .brand img {
            width: 52px;
            height: 52px;
            object-fit: contain;
            background: white;
            border-radius: 9px;
            padding: 4px;
        }

        .brand h2 {
            font-size: 15px;
            margin: 0;
            letter-spacing: .5px;
        }

        .brand span {
            display: block;
            font-size: 11px;
            color: #bfdbfe;
            margin-top: 5px;
        }

        .user-info {
            padding: 22px 8px;
            border-bottom: 1px solid rgba(255,255,255,.15);
        }

        .user-info strong {
            display: block;
            font-size: 14px;
        }

        .user-info span {
            display: block;
            font-size: 12px;
            color: #bfdbfe;
            margin-top: 6px;
        }

        .menu-title {
            font-size: 11px;
            color: #93c5fd;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 25px 10px 12px;
        }

        .sidebar nav {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .sidebar nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 12px;
            color: #e2e8f0;
            text-decoration: none;
            font-size: 14px;
            border-radius: 8px;
            transition: .2s;
        }

        .sidebar nav a:hover,
        .sidebar nav a.active {
            background: #2563eb;
            color: white;
        }

        .menu-icon {
            width: 22px;
            text-align: center;
            font-size: 17px;
        }

        .sidebar-bottom {
            margin-top: auto;
            padding-top: 25px;
        }

        .logout-button {
            width: 100%;
            padding: 12px;
            border: 1px solid rgba(255,255,255,.25);
            background: transparent;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }

        .logout-button:hover {
            background: #dc2626;
            border-color: #dc2626;
        }

        /* CONTENIDO PRINCIPAL */

        .main-content {
            flex: 1;
            min-width: 0;
        }

        .topbar {
            height: 75px;
            background: white;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 35px;
        }

        .topbar h1 {
            font-size: 21px;
            color: #172554;
            margin: 0;
        }

        .topbar span {
            font-size: 13px;
            color: #64748b;
        }

        .content {
            padding: 35px;
        }

        /* RESPONSIVE */

        @media (max-width: 800px) {
            .dashboard-layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                min-width: 0;
            }

            .sidebar nav {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .sidebar-bottom {
                margin-top: 20px;
            }

            .content {
                padding: 20px;
            }

            .topbar {
                padding: 0 20px;
            }
        }

        @media (max-width: 450px) {
            .sidebar nav {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<div class="dashboard-layout">

    <aside class="sidebar">

        {{-- LOGO --}}
        <div class="brand">

            <img src="{{ asset('images/image.png') }}" alt="Logo JENALEX">

            <div>
                <h2>JENALEX DYNAMICS</h2>
                <span>Gestión tecnológica</span>
            </div>

        </div>

        {{-- INFORMACIÓN DEL USUARIO --}}
        <div class="user-info">

            <strong>{{ auth()->user()->name }}</strong>

            <span>{{ auth()->user()->roleName() }}</span>

        </div>

        <p class="menu-title">Menú principal</p>

        <nav>

            {{-- INICIO --}}
            <a href="{{ route('dashboard') }}"
               class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">

                <span class="menu-icon">⌂</span>
                Inicio

            </a>

            {{-- USUARIOS --}}
            @can('manage-users')

                <a href="{{ route('usuarios.index') }}"
                   class="{{ request()->routeIs('usuarios.*') ? 'active' : '' }}">

                    <span class="menu-icon">♙</span>
                    Usuarios

                </a>

            @endcan

            {{-- INVENTARIO --}}
            @can('view-inventory')

                <a href="{{ route('equipos.index') }}"
                   class="{{ request()->routeIs('equipos.*') ? 'active' : '' }}">

                    <span class="menu-icon">▣</span>

                    {{ auth()->user()->role === 'usuario' ? 'Mis equipos' : 'Inventario' }}

                </a>

            @endcan

            {{-- MESA DE AYUDA --}}
            @if (
                auth()->user()->hasPermission('register-requests') ||
                auth()->user()->hasPermission('assign-requests') ||
                auth()->user()->hasPermission('attend-requests') ||
                auth()->user()->hasPermission('view-all-request-tracking') ||
                auth()->user()->hasPermission('view-own-request-tracking')
            )

                <a href="{{ route('solicitudes.index') }}"
                   class="{{ request()->routeIs('solicitudes.*') ? 'active' : '' }}">

                    <span class="menu-icon">🎧</span>
                    Mesa de ayuda

                </a>

            @endif

            {{-- REGISTRAR SOLICITUD --}}
            @can('register-requests')

                <a href="{{ route('solicitudes.create') }}"
                   class="{{ request()->routeIs('solicitudes.create') ? 'active' : '' }}">

                    <span class="menu-icon">＋</span>
                    Registrar solicitud

                </a>

            @endcan

            {{-- MANTENIMIENTOS --}}
            @can('register-maintenance')

                <a href="{{ route('mantenimientos.index') }}"
                   class="{{ request()->routeIs('mantenimientos.*') ? 'active' : '' }}">

                    <span class="menu-icon">⚙</span>
                    Mantenimientos

                </a>

            @endcan

            {{-- REPORTES --}}
            @can('generate-reports')

                <a href="{{ route('pdf.general') }}" target="_blank">

                    <span class="menu-icon">▤</span>
                    Reportes

                </a>

            @endcan

        </nav>

        {{-- CERRAR SESIÓN --}}
        <div class="sidebar-bottom">

            <form action="{{ route('logout') }}" method="POST">

                @csrf

                <button type="submit" class="logout-button">
                    ↪ Cerrar sesión
                </button>

            </form>

        </div>

    </aside>

    {{-- CONTENIDO --}}
    <main class="main-content">

        <header class="topbar">

            <h1>@yield('page-title', 'Inicio')</h1>

            <span>JENALEX DYNAMICS</span>

        </header>

        <section class="content">

            @yield('content')

        </section>

    </main>

</div>

</body>
</html>
