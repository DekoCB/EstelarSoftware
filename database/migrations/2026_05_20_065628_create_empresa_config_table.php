<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresa_config', function (Blueprint $table) {
            $table->id();

            // Datos fiscales
            $table->string('ruc', 11)->nullable();
            $table->string('razon_social', 200)->nullable();
            $table->string('nombre_comercial', 200)->nullable();
            $table->text('direccion')->nullable();
            $table->string('ubigeo', 6)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('web', 200)->nullable();

            // Logos (paths relativos en storage/app/public/)
            // Sidebar  : 200×200px — PNG/SVG transparente
            // Login     : 400×400px — PNG/SVG transparente
            // Documentos: 400×150px — PNG transparente (encabezado A4)
            $table->string('logo_sidebar')->nullable();
            $table->string('logo_login')->nullable();
            $table->string('logo_documentos')->nullable();

            // Facturación SUNAT
            $table->decimal('igv_porcentaje', 5, 2)->default(18.00);
            $table->string('moneda', 3)->default('PEN');
            $table->string('serie_boleta', 10)->default('B001');
            $table->string('serie_factura', 10)->default('F001');
            $table->enum('sunat_modo', ['sandbox', 'produccion'])->default('sandbox');
            $table->string('nubefact_url')->nullable()->default('https://api.nubefact.com/api/v1');
            $table->string('nubefact_token')->nullable();

            // Certificado digital PFX (paths en storage/app privado)
            $table->string('certificado_pfx_path')->nullable();
            $table->text('certificado_pfx_clave')->nullable(); // encrypted

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresa_config');
    }
};
