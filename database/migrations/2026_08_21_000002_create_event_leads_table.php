<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_leads', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();

            $table->enum('tipo_documento', ['DNI', 'RUC', 'CE', 'PASAPORTE'])->default('DNI');
            $table->string('numero_documento', 15)->nullable();
            $table->string('nombres', 200);
            $table->string('empresa', 200)->nullable();

            $table->string('email', 150)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->text('direccion')->nullable();

            // Ubicación precisa capturada por GPS al momento de registrar el lead
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->decimal('precision_metros', 8, 2)->nullable();

            $table->text('interes')->nullable();
            $table->enum('estado', ['nuevo', 'contactado', 'convertido', 'descartado'])->default('nuevo');

            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index('estado');
            $table->index('numero_documento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_leads');
    }
};
