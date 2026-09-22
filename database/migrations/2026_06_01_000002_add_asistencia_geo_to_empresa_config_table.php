<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresa_config', function (Blueprint $table) {
            $table->decimal('asistencia_latitud',  10, 7)->nullable()->after('web');
            $table->decimal('asistencia_longitud', 10, 7)->nullable()->after('asistencia_latitud');
            $table->unsignedInteger('asistencia_radio_metros')->default(15)->after('asistencia_longitud');
        });
    }

    public function down(): void
    {
        Schema::table('empresa_config', function (Blueprint $table) {
            $table->dropColumn(['asistencia_latitud', 'asistencia_longitud', 'asistencia_radio_metros']);
        });
    }
};
