
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignaciones', function (Blueprint $table) {

            $table->id();

            // Equipo que se entrega
            $table->foreignId('equipo_id')
                ->constrained('equipos')
                ->restrictOnDelete();

            // Usuario que recibe el equipo
            $table->foreignId('usuario_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Usuario que registra la entrega
            $table->foreignId('entregado_por')
                ->constrained('users')
                ->restrictOnDelete();

            // Fecha de entrega
            $table->dateTime('fecha_asignacion');

            // Fecha de devolución
            $table->dateTime('fecha_devolucion')
                ->nullable();

            // Usuario que recibe el equipo devuelto
            $table->foreignId('recibido_por')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            // Observaciones de la entrega o devolución
            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignaciones');
    }
};
