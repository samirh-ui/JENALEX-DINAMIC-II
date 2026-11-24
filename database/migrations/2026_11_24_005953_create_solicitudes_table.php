<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();

            // Identificación de la solicitud
            $table->string('codigo', 30)->unique();

            // Equipo y usuario que reporta el problema
            $table->foreignId('equipo_id')
                ->constrained('equipos')
                ->restrictOnDelete();

            $table->foreignId('usuario_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Información del problema
            $table->string('tipo', 100);
            $table->text('descripcion');

            // Gestión de Mesa de ayuda
            $table->string('estado', 30)->default('pendiente');
            $table->string('prioridad', 20)->nullable();

            $table->foreignId('tecnico_id')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('escalado_por')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamp('fecha_escalamiento')->nullable();
            $table->timestamp('fecha_asignacion')->nullable();

            // Atención técnica
            $table->text('diagnostico')->nullable();
            $table->text('solucion')->nullable();

            $table->timestamp('fecha_respuesta')->nullable();
            $table->timestamp('fecha_solucion')->nullable();
            $table->timestamp('fecha_cierre')->nullable();

            // Fechas límite para el seguimiento del ANS
            $table->timestamp('ans_respuesta_limite')->nullable();
            $table->timestamp('ans_solucion_limite')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
