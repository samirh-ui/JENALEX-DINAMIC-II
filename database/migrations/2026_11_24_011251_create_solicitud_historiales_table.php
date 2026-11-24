<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitud_historiales', function (Blueprint $table) {
            $table->id();

            // Solicitud a la que pertenece el movimiento
            $table->foreignId('solicitud_id')
                ->constrained('solicitudes')
                ->restrictOnDelete();

            // Usuario que realizó el movimiento
            $table->foreignId('usuario_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Tipo de movimiento realizado
            $table->string('accion', 50);

            // Valores anteriores y nuevos
            $table->string('estado_anterior', 30)->nullable();
            $table->string('estado_nuevo', 30)->nullable();

            $table->string('prioridad_anterior', 20)->nullable();
            $table->string('prioridad_nueva', 20)->nullable();

            $table->unsignedBigInteger('tecnico_anterior')->nullable();
            $table->unsignedBigInteger('tecnico_nuevo')->nullable();

            // Observaciones del movimiento
            $table->text('observacion')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitud_historiales');
    }
};
