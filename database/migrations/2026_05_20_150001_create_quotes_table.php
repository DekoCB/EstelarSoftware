<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 20)->unique();
            $table->foreignId('client_id')->constrained('clients');
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->enum('status', ['borrador', 'enviado', 'aceptado', 'rechazado', 'facturado'])->default('borrador');
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento')->nullable();
            $table->enum('moneda', ['PEN', 'USD'])->default('PEN');
            $table->decimal('tipo_cambio', 8, 3)->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('igv', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->boolean('incluye_igv')->default(true);
            $table->text('notas')->nullable();
            $table->text('terminos')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
