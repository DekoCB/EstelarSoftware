<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_phases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')->constrained()->cascadeOnDelete();

            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('order')->default(0);
            $table->unsignedTinyInteger('progress')->default(0);

            $table->enum('status', [
                'pendiente', 'en_curso', 'completada', 'cancelada',
            ])->default('pendiente');

            $table->timestamps();

            $table->index(['project_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_phases');
    }
};
