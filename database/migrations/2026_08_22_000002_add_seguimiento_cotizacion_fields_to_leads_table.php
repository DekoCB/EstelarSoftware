<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->boolean('reunion_realizada')->default(false)->after('ruc_dni');
            $table->boolean('cotizacion_enviada')->default(false)->after('reunion_realizada');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['reunion_realizada', 'cotizacion_enviada']);
        });
    }
};
