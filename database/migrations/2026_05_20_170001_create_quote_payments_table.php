<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quote_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained('quotes')->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();

            $table->string('nombre', 150);           // "Anticipo", "2da cuota", "Cuota final"
            $table->decimal('porcentaje', 5, 2);     // 40.00, 30.00, 30.00
            $table->decimal('monto', 12, 2);         // calculado del total de la cotización
            $table->date('fecha_vencimiento')->nullable();
            $table->timestamp('fecha_pago')->nullable();
            $table->enum('estado', ['pendiente', 'vencida', 'pagada'])->default('pendiente');
            $table->string('metodo_pago', 80)->nullable(); // Transferencia, Yape, Efectivo, etc.
            $table->text('notas')->nullable();
            $table->unsignedSmallInteger('orden')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_payments');
    }
};
