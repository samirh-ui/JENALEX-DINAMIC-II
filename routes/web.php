
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EquipoController;

Route::get('/', function () {
    return redirect()->route('equipos.index');
});

Route::resource('equipos', EquipoController::class);
