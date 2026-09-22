<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresa_config', function (Blueprint $table) {
            $table->unsignedInteger('alerta_despues_minutos')->default(5)->after('recordatorio_reunion_minutos');
        });

        Schema::table('lead_meetings', function (Blueprint $table) {
            $table->timestamp('aviso_hora_enviado_en')->nullable()->after('recordatorio_enviado_en');
            $table->timestamp('aviso_despues_en')->nullable()->after('aviso_hora_enviado_en');
            $table->timestamp('aviso_despues_enviado_en')->nullable()->after('aviso_despues_en');
        });
    }

    public function down(): void
    {
        Schema::table('lead_meetings', function (Blueprint $table) {
            $table->dropColumn(['aviso_hora_enviado_en', 'aviso_despues_en', 'aviso_despues_enviado_en']);
        });

        Schema::table('empresa_config', function (Blueprint $table) {
            $table->dropColumn('alerta_despues_minutos');
        });
    }
};
