<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            $table->enum('tipo_documento', ['RUC', 'DNI', 'CE', 'PASAPORTE'])->default('RUC');
            $table->string('numero_documento', 15)->unique();
            $table->string('razon_social', 200);
            $table->string('nombre_comercial', 200)->nullable();

            $table->string('email', 150)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->text('direccion')->nullable();
            $table->string('ubigeo', 6)->nullable();

            $table->enum('estado', ['prospecto', 'activo', 'inactivo', 'bloqueado'])->default('prospecto');

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index('estado');
            $table->index('tipo_documento');
            $table->index('razon_social');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
