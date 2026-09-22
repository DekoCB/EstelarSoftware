<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_movements', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['ingreso', 'egreso']);
            $table->string('concepto');
            $table->text('descripcion')->nullable();
            $table->decimal('monto', 12, 2);
            $table->string('moneda', 3)->default('PEN');
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'yape', 'plin', 'tarjeta', 'cheque', 'otro'])->default('efectivo');
            $table->string('referencia')->nullable();
            $table->date('fecha');
            $table->enum('categoria', [
                'cobro_cliente', 'anticipo_cliente', 'otro_ingreso',
                'pago_proveedor', 'planilla', 'servicios', 'equipos', 'impuestos', 'otro_egreso',
            ]);
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->foreignId('quote_id')->nullable()->constrained('quotes')->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->string('comprobante_path')->nullable();
            $table->text('notas')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_movements');
    }
};
