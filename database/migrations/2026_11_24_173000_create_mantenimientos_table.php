
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mantenimientos', function (Blueprint $table) {
            $table->id();

            // Equipo que recibe el mantenimiento
            $table->foreignId('equipo_id')
                ->constrained('equipos')
                ->cascadeOnDelete();

            // Técnico responsable
            $table->foreignId('tecnico_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Información del mantenimiento
            $table->enum('tipo', [
                'preventivo',
                'correctivo'
            ]);

            $table->enum('estado', [
                'pendiente',
                'en_proceso',
                'finalizado'
            ])->default('pendiente');

            $table->dateTime('fecha_inicio')->nullable();

            $table->dateTime('fecha_finalizacion')->nullable();

            $table->text('diagnostico')->nullable();

            $table->text('trabajo_realizado')->nullable();

            // Estado que tenía el equipo antes del mantenimiento
            $table->string('estado_anterior')->nullable();

            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mantenimientos');
    }
};
