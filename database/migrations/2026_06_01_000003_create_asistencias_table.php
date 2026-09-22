<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('tipo', ['entrada', 'salida']);
            $table->date('fecha');
            $table->time('hora');
            $table->decimal('latitud',  10, 7);
            $table->decimal('longitud', 10, 7);
            $table->decimal('distancia_metros', 8, 2);
            $table->unsignedInteger('radio_configurado');
            $table->string('ip_address', 45)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'fecha', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
