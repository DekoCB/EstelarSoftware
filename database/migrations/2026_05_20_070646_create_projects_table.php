<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('quote_id')->nullable(); // FK a quotes se agrega en Sprint 3

            $table->string('name', 200);
            $table->text('description')->nullable();

            $table->enum('status', [
                'planificado', 'en_curso', 'pausado',
                'en_revision', 'entregado', 'cancelado',
            ])->default('planificado');

            $table->unsignedTinyInteger('progress')->default(0);

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->foreignId('responsible_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('client_id');
            $table->index('responsible_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
