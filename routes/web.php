
<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AsignacionController;
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
    Route::get('/equipos/pdf', [PdfController::class, 'equipos'])
        ->middleware('can:generate-reports')->name('pdf.equipos');
    Route::get('/equipos/create', [EquipoController::class, 'create'])
        ->middleware('can:register-equipment')->name('equipos.create');
    Route::post('/equipos', [EquipoController::class, 'store'])
        ->middleware('can:register-equipment')->name('equipos.store');
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

});
