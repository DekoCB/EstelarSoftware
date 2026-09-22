<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('lugar', 150)->nullable()->after('empresa');
            $table->string('respuesta_rapida', 30)->nullable()->after('email');
            $table->text('respuesta_comentario')->nullable()->after('respuesta_rapida');
            $table->date('respuesta_fecha')->nullable()->after('respuesta_comentario');
            $table->time('respuesta_hora')->nullable()->after('respuesta_fecha');
            $table->string('sistema_interes', 200)->nullable()->after('respuesta_hora');

            $table->index('respuesta_rapida');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['respuesta_rapida']);
            $table->dropColumn([
                'lugar', 'respuesta_rapida', 'respuesta_comentario',
                'respuesta_fecha', 'respuesta_hora', 'sistema_interes',
            ]);
        });
    }
};
