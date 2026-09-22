<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportes_diarios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('area', ['desarrollo', 'ventas']);
            $table->date('fecha');

            $table->string('proyectos_asignados')->nullable();
            $table->string('sprint_iteracion')->nullable();
            $table->string('modulo_componente')->nullable();
            $table->decimal('horas_trabajadas', 4, 1)->default(8);

            // JSON: [{descripcion, tipo, estado, tiempo_horas}]
            $table->json('tareas');

            $table->text('logros_destacados')->nullable();

            // JSON: [{descripcion, impacto, requiere_apoyo}]
            $table->json('impedimentos')->nullable();

            $table->text('plan_siguiente_dia')->nullable();

            $table->string('archivo_adjunto')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'fecha']);
            $table->index(['area', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes_diarios');
    }
};
