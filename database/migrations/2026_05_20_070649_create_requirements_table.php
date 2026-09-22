<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requirements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('phase_id')->nullable()->constrained('project_phases')->nullOnDelete();

            $table->string('title', 200);
            $table->text('description')->nullable();

            $table->enum('type', ['funcional', 'tecnico', 'negocio', 'ux_ui'])->default('funcional');
            $table->enum('priority', ['critica', 'alta', 'media', 'baja'])->default('media');
            $table->enum('status', [
                'pendiente', 'en_progreso', 'en_revision', 'completado', 'rechazado',
            ])->default('pendiente');

            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['project_id', 'status']);
            $table->index('priority');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requirements');
    }
};
