<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('nombre', 150);
            $table->string('logo')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'nombre']);
        });

        Schema::table('event_attendees', function (Blueprint $table) {
            $table->foreignId('event_team_id')->nullable()->after('event_id')
                ->constrained('event_teams')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('event_attendees', function (Blueprint $table) {
            $table->dropConstrainedForeignId('event_team_id');
        });

        Schema::dropIfExists('event_teams');
    }
};
