<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->text('mensaje')->nullable()->after('cotizacion_enviada');
            $table->string('origen', 20)->default('manual')->after('mensaje');

            $table->index('origen');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['origen']);
            $table->dropColumn(['mensaje', 'origen']);
        });
    }
};
