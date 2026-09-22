<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requirements', function (Blueprint $table) {
            $table->foreignId('sprint_id')
                  ->nullable()
                  ->after('phase_id')
                  ->constrained('sprints')
                  ->nullOnDelete();

            $table->unsignedTinyInteger('story_points')
                  ->nullable()
                  ->after('sprint_id')
                  ->comment('Fibonacci: 1,2,3,5,8,13,21');
        });
    }

    public function down(): void
    {
        Schema::table('requirements', function (Blueprint $table) {
            $table->dropForeign(['sprint_id']);
            $table->dropColumn(['sprint_id', 'story_points']);
        });
    }
};
