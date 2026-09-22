<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresa_config', function (Blueprint $table) {
            $table->unsignedInteger('recordatorio_reunion_minutos')->default(15)->after('asistencia_radio_metros');
        });
    }

    public function down(): void
    {
        Schema::table('empresa_config', function (Blueprint $table) {
            $table->dropColumn('recordatorio_reunion_minutos');
        });
    }
};
