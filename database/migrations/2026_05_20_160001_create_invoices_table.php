<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            // Identificación del comprobante
            $table->enum('tipo_comprobante', ['01', '03']); // 01=Factura, 03=Boleta
            $table->string('serie', 4);                     // F001 / B001
            $table->string('correlativo', 8)->nullable();   // 00000001 (asignado al emitir)
            $table->string('numero_completo', 20)->nullable()->unique(); // F001-00000001

            // Referencia al ID en la API SUNAT externa
            $table->unsignedBigInteger('sunat_doc_id')->nullable();

            // Relaciones internas
            $table->foreignId('client_id')->constrained('clients');
            $table->foreignId('quote_id')->nullable()->constrained('quotes')->nullOnDelete();

            // Fechas
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento')->nullable();

            // Moneda y totales
            $table->enum('moneda', ['PEN', 'USD'])->default('PEN');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('igv', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            // Estado SUNAT
            $table->enum('estado_sunat', [
                'borrador',   // solo local, no enviado a la API aún
                'pendiente',  // creado en API, esperando envío
                'enviando',   // en proceso de envío
                'aceptado',   // aceptado por SUNAT
                'rechazado',  // rechazado por SUNAT
                'anulado',    // anulado
                'error',      // error de comunicación
            ])->default('borrador');

            $table->text('sunat_mensaje')->nullable(); // mensaje de SUNAT o error
            $table->text('notas')->nullable();

            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('emitido_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['tipo_comprobante', 'serie']);
            $table->index('estado_sunat');
            $table->index('fecha_emision');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
