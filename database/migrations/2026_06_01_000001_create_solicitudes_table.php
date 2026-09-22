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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('tipo', ['dinero', 'objeto']);
            $table->string('titulo');
            $table->text('descripcion');
            $table->decimal('monto', 10, 2)->nullable();
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado', 'entregado'])->default('pendiente');
            $table->text('respuesta_admin')->nullable();
            $table->foreignId('atendido_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('atendido_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
