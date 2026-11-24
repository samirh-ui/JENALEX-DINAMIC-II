<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\MantenimientoController;





Route::get('/', function () {
    return redirect()->route('equipos.index');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])
        ->middleware('throttle:5,1')->name('login.store');
});

    Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::get('/equipos', [EquipoController::class, 'index'])
        ->middleware('can:view-inventory')->name('equipos.index');
    Route::get('/equipos/create', [EquipoController::class, 'create'])
        ->middleware('can:register-equipment')->name('equipos.create');
    Route::post('/equipos', [EquipoController::class, 'store'])
        ->middleware('can:register-equipment')->name('equipos.store');
    // REPORTES PDF
    Route::middleware('can:generate-reports')->group(function () {

    // Reporte de equipos
    Route::get('/equipos/pdf', [PdfController::class, 'equipos'])
        ->name('pdf.equipos');

    // Reporte de asignaciones y devoluciones
    Route::get('/asignaciones/pdf', [PdfController::class, 'asignaciones'])
        ->name('pdf.asignaciones');

    // Reporte de solicitudes de soporte
    Route::get('/solicitudes/pdf', [PdfController::class, 'solicitudes'])
        ->name('pdf.solicitudes');

    // Reporte de mantenimientos
    Route::get('/mantenimientos/pdf', [PdfController::class, 'mantenimientos'])
        ->name('pdf.mantenimientos');

    // Reporte general
    Route::get('/reportes/general/pdf', [PdfController::class, 'general'])
        ->name('pdf.general');

});
    Route::get('/equipos/{equipo}', [EquipoController::class, 'show'])
        ->middleware('can:view-inventory')->name('equipos.show');
    Route::get('/equipos/{equipo}/edit', [EquipoController::class, 'edit'])
        ->middleware('can:edit-equipment')->name('equipos.edit');
    Route::put('/equipos/{equipo}', [EquipoController::class, 'update'])
        ->middleware('can:edit-equipment')->name('equipos.update');
    Route::delete('/equipos/{equipo}', [EquipoController::class, 'destroy'])
        ->middleware('can:delete-equipment')->name('equipos.destroy');

    Route::resource('usuarios', UserController::class)
        ->middleware('can:manage-users');

    // Registrar la entrega de un equipo
    Route::post('/equipos/{equipo}/asignaciones', [
    AsignacionController::class,
    'store'
])
    ->middleware('can:assign-equipment')
    ->name('asignaciones.store');

     // Registrar la devolución de un equipo
    Route::put('/asignaciones/{asignacion}/devolver', [
    AsignacionController::class,
    'devolver'
  ])
    ->middleware('can:assign-equipment')
    ->name('asignaciones.devolver');

    // Registrar solicitudes de soporte
    Route::get('/solicitudes/crear', [SolicitudController::class, 'create'])
    ->middleware('can:register-requests')
    ->name('solicitudes.create');

        Route::post('/solicitudes', [SolicitudController::class, 'store'])
    ->middleware('can:register-requests')
    ->name('solicitudes.store');

    Route::get('/solicitudes', [SolicitudController::class, 'index'])
    ->name('solicitudes.index');

    Route::get('/solicitudes/{solicitud}', [
    SolicitudController::class,
    'show'
])->name('solicitudes.show');   

    Route::put('/solicitudes/{solicitud}/asignar', [
    SolicitudController::class,
    'asignar'
])
    ->middleware('can:assign-requests')
    ->name('solicitudes.asignar');

    Route::put(
    '/solicitudes/{solicitud}/atencion',
    [SolicitudController::class, 'actualizarAtencion']
)
    ->middleware('can:attend-requests')
    ->name('solicitudes.atencion');

    Route::put(
    '/solicitudes/{solicitud}/cerrar',
    [SolicitudController::class, 'cerrar']
)
    ->middleware('can:assign-requests')
    ->name('solicitudes.cerrar');

    Route::put(
    '/solicitudes/{solicitud}/devolver',
    [SolicitudController::class, 'devolver']
)
    ->middleware('can:assign-requests')
    ->name('solicitudes.devolver');

    
    
// MÓDULO DE MANTENIMIENTOS

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

});
