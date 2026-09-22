<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quote_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained('quotes')->cascadeOnDelete();
            $table->string('descripcion');
            $table->decimal('cantidad', 10, 2)->default(1);
            $table->string('unidad', 30)->default('servicio');
            $table->decimal('precio_unitario', 12, 2)->default(0);
            $table->decimal('descuento', 5, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_items');
    }
};
