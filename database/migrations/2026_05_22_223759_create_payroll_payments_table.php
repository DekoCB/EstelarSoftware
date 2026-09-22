<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');         // quién recibe
            $table->foreignId('created_by')->constrained('users');      // quién registró
            $table->foreignId('cash_movement_id')->nullable()->constrained('cash_movements')->nullOnDelete();

            $table->string('periodo', 7);                               // formato YYYY-MM
            $table->enum('tipo', ['sueldo', 'honorario', 'comision', 'bono', 'adelanto', 'otro']);
            $table->string('concepto');
            $table->decimal('monto', 12, 2);
            $table->string('moneda', 3)->default('PEN');
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'yape', 'plin', 'tarjeta', 'cheque', 'otro'])->nullable();
            $table->enum('estado', ['pendiente', 'pagado'])->default('pendiente');
            $table->date('fecha_pago')->nullable();
            $table->text('notas')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'periodo']);
            $table->index(['estado', 'periodo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_payments');
    }
};
