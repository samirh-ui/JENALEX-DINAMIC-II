
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('documento', 30)->unique();
            $table->string('telefono', 20)->nullable();
            $table->string('cargo', 100);
            $table->string('estado', 20)->default('activo');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['documento']);
            $table->dropColumn([
                'documento',
                'telefono',
                'cargo',
                'estado'
            ]);
        });
    }
};

