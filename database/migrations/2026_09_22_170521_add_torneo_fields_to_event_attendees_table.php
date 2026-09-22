<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_attendees', function (Blueprint $table) {
            $table->string('nickname', 100)->nullable()->after('nombres');
            $table->unsignedTinyInteger('edad')->nullable()->after('nickname');
            $table->string('steam_id', 50)->nullable()->after('telefono');
            $table->string('equipo', 150)->nullable()->after('steam_id');
            $table->string('rol', 20)->nullable()->after('equipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_attendees', function (Blueprint $table) {
            $table->dropColumn(['nickname', 'edad', 'steam_id', 'equipo', 'rol']);
        });
    }
};
