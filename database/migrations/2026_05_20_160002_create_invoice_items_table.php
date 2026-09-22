<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('descripcion');
            $table->string('unidad_sunat', 3)->default('ZZ'); // ZZ=Servicios, NIU=Unidad
            $table->decimal('cantidad', 10, 2)->default(1);
            $table->decimal('precio_unitario', 12, 2)->default(0);
            $table->string('tipo_afectacion', 2)->default('10'); // 10=Gravado, 20=Exonerado, 30=Inafecto
            $table->decimal('igv_porcentaje', 5, 2)->default(18);
            $table->decimal('subtotal', 12, 2)->default(0); // sin IGV
            $table->decimal('igv', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);    // con IGV
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
