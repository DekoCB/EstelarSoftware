<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quote_payments', function (Blueprint $table) {
            $table->foreignId('cash_movement_id')
                  ->nullable()
                  ->after('invoice_id')
                  ->constrained('cash_movements')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('quote_payments', function (Blueprint $table) {
            $table->dropForeign(['cash_movement_id']);
            $table->dropColumn('cash_movement_id');
        });
    }
};
