<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->boolean('quiere_cotizacion')->default(false)->after('sistema_interes');
            $table->string('ruc_dni', 11)->nullable()->after('quiere_cotizacion');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['quiere_cotizacion', 'ruc_dni']);
        });
    }
};
