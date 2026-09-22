<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('justificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('tipo', ['entrada', 'salida']);
            $table->date('fecha');
            $table->time('hora_justificada');
            $table->text('motivo');
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->text('respuesta_admin')->nullable();
            $table->foreignId('atendido_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('atendido_at')->nullable();
            $table->foreignId('asistencia_id')->nullable()->constrained('asistencias')->nullOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'fecha', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('justificaciones');
    }
};
