<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lead_meetings', function (Blueprint $table) {
            $table->unsignedSmallInteger('recordatorio_minutos')->nullable()->after('nota');
            $table->dateTime('recordatorio_en')->nullable()->after('recordatorio_minutos');
            $table->dateTime('recordatorio_enviado_en')->nullable()->after('recordatorio_en');

            $table->index('recordatorio_en');
        });
    }

    public function down(): void
    {
        Schema::table('lead_meetings', function (Blueprint $table) {
            $table->dropIndex(['recordatorio_en']);
            $table->dropColumn(['recordatorio_minutos', 'recordatorio_en', 'recordatorio_enviado_en']);
        });
    }
};
