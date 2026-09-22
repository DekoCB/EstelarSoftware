<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_attendees', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_id')->constrained()->cascadeOnDelete();

            $table->string('codigo', 20)->unique();
            $table->uuid('qr_token')->unique();

            $table->string('nombres', 200);
            $table->string('empresa', 200)->nullable();
            $table->string('tipo_documento', 20)->nullable();
            $table->string('numero_documento', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('telefono', 20)->nullable();

            $table->enum('estado', ['registrado', 'asistio', 'cancelado'])->default('registrado');
            $table->timestamp('checked_in_at')->nullable();
            $table->foreignId('checked_in_by')->nullable()->constrained('users')->nullOnDelete();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->unique(['event_id', 'numero_documento']);
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_attendees');
    }
};
