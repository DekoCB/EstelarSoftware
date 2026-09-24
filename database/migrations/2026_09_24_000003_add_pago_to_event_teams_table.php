<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_teams', function (Blueprint $table) {
            // Token publico para que el equipo suba su comprobante sin
            // login (el id autoincremental seria adivinable).
            $table->uuid('token')->nullable()->after('event_id');
            $table->enum('estado_pago', ['pendiente', 'en_revision', 'pagado', 'rechazado'])
                ->default('pendiente')->after('logo');
            $table->string('comprobante_pago')->nullable()->after('estado_pago');
            $table->timestamp('comprobante_subido_at')->nullable()->after('comprobante_pago');
            $table->timestamp('pago_revisado_at')->nullable()->after('comprobante_subido_at');
            $table->foreignId('pago_revisado_by')->nullable()->after('pago_revisado_at')
                ->constrained('users')->nullOnDelete();
        });

        DB::table('event_teams')->whereNull('token')->pluck('id')->each(
            fn ($id) => DB::table('event_teams')->where('id', $id)->update(['token' => (string) Str::uuid()])
        );

        Schema::table('event_teams', function (Blueprint $table) {
            $table->uuid('token')->nullable(false)->change();
            $table->unique('token');
            $table->index('estado_pago');
        });
    }

    public function down(): void
    {
        Schema::table('event_teams', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pago_revisado_by');
            $table->dropUnique(['token']);
            $table->dropIndex(['estado_pago']);
            $table->dropColumn(['token', 'estado_pago', 'comprobante_pago', 'comprobante_subido_at', 'pago_revisado_at']);
        });
    }
};
