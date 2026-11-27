<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\MantenimientoController;
use Illuminate\Support\Facades\Route;

// ======================================================
// RUTA PRINCIPAL
// ======================================================

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// ======================================================
// AUTENTICACIÓN
// ======================================================

Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'create'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('login.store');

});

// ======================================================
// RUTAS PARA USUARIOS AUTENTICADOS
// ======================================================

Route::middleware('auth')->group(function () {

    // Cerrar sesión
    Route::post('/logout', [AuthController::class, 'destroy'])
        ->name('logout');

    // ==================================================
    // DASHBOARD PRINCIPAL
    // ==================================================

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // ==================================================
    // MÓDULO DE EQUIPOS
    // ==================================================

    Route::get('/equipos', [EquipoController::class, 'index'])
        ->middleware('can:view-inventory')
        ->name('equipos.index');

    Route::get('/equipos/create', [EquipoController::class, 'create'])
        ->middleware('can:register-equipment')
        ->name('equipos.create');

    Route::post('/equipos', [EquipoController::class, 'store'])
        ->middleware('can:register-equipment')
        ->name('equipos.store');

    Route::get('/equipos/{equipo}', [EquipoController::class, 'show'])
        ->middleware('can:view-inventory')
        ->name('equipos.show');

    Route::get('/equipos/{equipo}/edit', [EquipoController::class, 'edit'])
        ->middleware('can:edit-equipment')
        ->name('equipos.edit');

    Route::put('/equipos/{equipo}', [EquipoController::class, 'update'])
        ->middleware('can:edit-equipment')
        ->name('equipos.update');

    Route::delete('/equipos/{equipo}', [EquipoController::class, 'destroy'])
        ->middleware('can:delete-equipment')
        ->name('equipos.destroy');

    // ==================================================
    // MÓDULO DE USUARIOS
    // ==================================================

    Route::resource('usuarios', UserController::class)
        ->middleware('can:manage-users');

    // ==================================================
    // MÓDULO DE ASIGNACIONES
    // ==================================================

    // Registrar entrega de equipo
    Route::post('/equipos/{equipo}/asignaciones', [
        AsignacionController::class,
        'store'
    ])
        ->middleware('can:assign-equipment')
        ->name('asignaciones.store');

    // Registrar devolución de equipo
    Route::put('/asignaciones/{asignacion}/devolver', [
        AsignacionController::class,
        'devolver'
    ])
        ->middleware('can:assign-equipment')
        ->name('asignaciones.devolver');

    // ==================================================
    // MÓDULO DE SOLICITUDES DE SOPORTE
    // ==================================================

    // Crear solicitud
    Route::get('/solicitudes/crear', [SolicitudController::class, 'create'])
        ->middleware('can:register-requests')
        ->name('solicitudes.create');

    Route::post('/solicitudes', [SolicitudController::class, 'store'])
        ->middleware('can:register-requests')
        ->name('solicitudes.store');

    // Consultar solicitudes
    Route::get('/solicitudes', [SolicitudController::class, 'index'])
        ->name('solicitudes.index');

    Route::get('/solicitudes/{solicitud}', [
        SolicitudController::class,
        'show'
    ])->name('solicitudes.show');

    // Asignar solicitud
    Route::put('/solicitudes/{solicitud}/asignar', [
        SolicitudController::class,
        'asignar'
    ])
        ->middleware('can:assign-requests')
        ->name('solicitudes.asignar');

    // Atender solicitud
    Route::put('/solicitudes/{solicitud}/atencion', [
        SolicitudController::class,
        'actualizarAtencion'
    ])
        ->middleware('can:attend-requests')
        ->name('solicitudes.atencion');

    // Cerrar solicitud
    Route::put('/solicitudes/{solicitud}/cerrar', [
        SolicitudController::class,
        'cerrar'
    ])
        ->middleware('can:assign-requests')
        ->name('solicitudes.cerrar');

    // Devolver solicitud al técnico
    Route::put('/solicitudes/{solicitud}/devolver', [
        SolicitudController::class,
        'devolver'
    ])
        ->middleware('can:assign-requests')
        ->name('solicitudes.devolver');

    // ==================================================
    // MÓDULO DE MANTENIMIENTOS
    // ==================================================

    // Consultar y registrar mantenimientos
    Route::middleware('can:register-maintenance')->group(function () {

        Route::get('/mantenimientos', [MantenimientoController::class, 'index'])
            ->name('mantenimientos.index');

        Route::get('/mantenimientos/crear', [MantenimientoController::class, 'create'])
            ->name('mantenimientos.create');

        Route::post('/mantenimientos', [MantenimientoController::class, 'store'])
            ->name('mantenimientos.store');

        Route::get('/mantenimientos/{mantenimiento}', [MantenimientoController::class, 'show'])
            ->name('mantenimientos.show');

    });

    // Editar y finalizar mantenimientos
    Route::middleware('can:manage-maintenance')->group(function () {

        Route::get('/mantenimientos/{mantenimiento}/editar', [MantenimientoController::class, 'edit'])
            ->name('mantenimientos.edit');

        Route::put('/mantenimientos/{mantenimiento}', [MantenimientoController::class, 'update'])
            ->name('mantenimientos.update');

        Route::put('/mantenimientos/{mantenimiento}/finalizar', [MantenimientoController::class, 'finalizar'])
            ->name('mantenimientos.finalizar');

    });

    // ==================================================
    // MÓDULO DE REPORTES PDF
    // ==================================================

    Route::middleware('can:generate-reports')->group(function () {

        Route::get('/equipos/pdf', [PdfController::class, 'equipos'])
            ->name('pdf.equipos');

        Route::get('/asignaciones/pdf', [PdfController::class, 'asignaciones'])
            ->name('pdf.asignaciones');

        Route::get('/solicitudes/pdf', [PdfController::class, 'solicitudes'])
            ->name('pdf.solicitudes');

        Route::get('/mantenimientos/pdf', [PdfController::class, 'mantenimientos'])
            ->name('pdf.mantenimientos');

        Route::get('/reportes/general/pdf', [PdfController::class, 'general'])
            ->name('pdf.general');

    });

});
