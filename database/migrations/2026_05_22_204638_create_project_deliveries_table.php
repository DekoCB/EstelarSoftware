<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients');
            $table->foreignId('created_by')->constrained('users');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->date('fecha_entrega');
            $table->enum('tipo', ['parcial', 'final'])->default('final');
            $table->enum('estado', ['borrador', 'firmado', 'observado'])->default('borrador');
            $table->text('items_entregados')->nullable();  // JSON con lista de ítems
            $table->text('observaciones')->nullable();
            $table->string('firma_cliente')->nullable();   // nombre del firmante
            $table->string('dni_firmante')->nullable();
            $table->string('cargo_firmante')->nullable();
            $table->timestamp('firmado_at')->nullable();
            $table->string('acta_path')->nullable();       // PDF generado en storage/app
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_deliveries');
    }
};
