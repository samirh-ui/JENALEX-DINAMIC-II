
@extends('layouts.dashboard')

@section('title', 'Inicio')
@section('page-title', 'Inicio')

@section('content')

<style>
    .welcome-panel {
        background: linear-gradient(120deg, #dbeafe, #eff6ff);
        border-radius: 18px;
        padding: 35px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 25px;
        margin-bottom: 35px;
        min-height: 210px;
    }

    .welcome-text h2 {
        color: #172554;
        font-size: 28px;
        margin: 0 0 12px;
    }

    .welcome-text p {
        color: #475569;
        font-size: 15px;
        line-height: 1.7;
        max-width: 550px;
        margin: 0;
    }

    .welcome-illustration {
        font-size: 90px;
        padding: 10px 25px;
    }

    .section-title {
        color: #172554;
        font-size: 21px;
        margin-bottom: 20px;
    }

    .access-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: 22px;
    }

    .access-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        min-height: 205px;
        box-shadow: 0 4px 15px rgba(15, 23, 42, .06);
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        text-decoration: none;
        transition: .2s;
    }

    .access-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 9px 22px rgba(15, 23, 42, .10);
        border-color: #93c5fd;
    }

    .access-icon {
        width: 55px;
        height: 55px;
        border-radius: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 27px;
        margin-bottom: 18px;
    }

    .icon-blue {
        background: #dbeafe;
    }

    .icon-green {
        background: #dcfce7;
    }

    .icon-purple {
        background: #ede9fe;
    }

    .access-card h3 {
        color: #172554;
        font-size: 18px;
        margin: 0 0 9px;
    }

    .access-card p {
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
        margin: 0 0 18px;
    }

    .access-link {
        color: #2563eb;
        font-size: 14px;
        font-weight: bold;
        margin-top: auto;
    }

    @media (max-width: 600px) {
        .welcome-panel {
            padding: 25px;
        }

        .welcome-text h2 {
            font-size: 23px;
        }

        .welcome-illustration {
            display: none;
        }
    }
</style>

{{-- PANEL DE BIENVENIDA --}}

<div class="welcome-panel">

    <div class="welcome-text">

        <h2>
            ¡Bienvenido, {{ auth()->user()->name }}!
        </h2>

    

    </div>

    <div class="welcome-illustration">
        💻
    </div>

</div>

{{-- ACCESOS PRINCIPALES --}}

<h2 class="section-title">Accesos principales</h2>

<div class="access-grid">

    {{-- EQUIPOS --}}

    @can('view-inventory')

        <a href="{{ route('equipos.index') }}" class="access-card">

            <div class="access-icon icon-blue">
                💻
            </div>

            <h3>
                {{ auth()->user()->role === 'usuario' ? 'Mis equipos' : 'Inventario de equipos' }}
            </h3>

            <p>
                {{ auth()->user()->role === 'usuario'
                    ? 'Consulta los equipos tecnológicos que tienes asignados.'
                    : 'Consulta y gestiona los equipos tecnológicos del inventario.' }}
            </p>

            <span class="access-link">Ingresar →</span>

        </a>

    @endcan

    {{-- SOLICITUDES --}}

    @if (
        auth()->user()->hasPermission('register-requests') ||
        auth()->user()->hasPermission('assign-requests') ||
        auth()->user()->hasPermission('attend-requests') ||
        auth()->user()->hasPermission('view-all-request-tracking') ||
        auth()->user()->hasPermission('view-own-request-tracking')
    )

        <a href="{{ route('solicitudes.index') }}" class="access-card">

            <div class="access-icon icon-green">
                🛠️
            </div>

            <h3>Solicitudes de soporte</h3>

            <p>
                Consulta y realiza seguimiento a las solicitudes
                de soporte técnico según tus permisos.
            </p>

            <span class="access-link">Ingresar →</span>

        </a>

    @endif

    {{-- REGISTRAR SOLICITUD --}}

    @can('register-requests')

        <a href="{{ route('solicitudes.create') }}" class="access-card">

            <div class="access-icon icon-purple">
                ✉️
            </div>

            <h3>Registrar solicitud</h3>

            <p>
                Registra una nueva solicitud para recibir
                asistencia técnica.
            </p>

            <span class="access-link">Ingresar →</span>

        </a>

    @endcan

    {{-- USUARIOS --}}

    @can('manage-users')

        <a href="{{ route('usuarios.index') }}" class="access-card">

            <div class="access-icon icon-blue">
                👥
            </div>

            <h3>Gestión de usuarios</h3>

            <p>
                Administra los usuarios, sus datos y sus roles
                dentro del sistema.
            </p>

            <span class="access-link">Ingresar →</span>

        </a>

    @endcan

    {{-- MANTENIMIENTOS --}}

    @can('register-maintenance')

        <a href="{{ route('mantenimientos.index') }}" class="access-card">

            <div class="access-icon icon-green">
                ⚙️
            </div>

            <h3>Mantenimientos</h3>

            <p>
                Consulta y registra mantenimientos de los
                equipos tecnológicos.
            </p>

            <span class="access-link">Ingresar →</span>

        </a>

    @endcan

    {{-- REPORTES --}}

    @can('generate-reports')

        <a href="{{ route('pdf.general') }}" target="_blank" class="access-card">

            <div class="access-icon icon-purple">
                📄
            </div>

            <h3>Mis reportes</h3>

            <p>
                Consulta y genera los reportes de gestión
                del sistema.
            </p>

            <span class="access-link">Ingresar →</span>

        </a>

    @endcan

</div>

@endsection
