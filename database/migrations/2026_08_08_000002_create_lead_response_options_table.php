<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_response_options', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 40)->unique();
            $table->string('label', 60);
            $table->string('color', 20);
            $table->boolean('requiere_fecha')->default(false);
            $table->boolean('requiere_hora')->default(false);
            $table->boolean('requiere_sistema')->default(false);
            $table->text('comentario_auto')->nullable();
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
        });

        $whatsappTexto = 'Al comunicarnos con el cliente, por motivos personales, solicitó escribir y brindar la información por WhatsApp.';

        $ahora = now();
        $base = [
            'requiere_fecha' => false, 'requiere_hora' => false, 'requiere_sistema' => false,
            'comentario_auto' => null, 'created_at' => $ahora, 'updated_at' => $ahora,
        ];
        DB::table('lead_response_options')->insert([
            $base + ['clave' => 'no_quiere',         'label' => 'No quiere',            'color' => 'red',     'orden' => 1],
            $base + ['clave' => 'no_contesta',       'label' => 'No contesta',          'color' => 'slate',   'orden' => 2],
            $base + ['clave' => 'en_duda',           'label' => 'En duda',              'color' => 'orange',  'orden' => 3],
            $base + ['clave' => 'apagado',           'label' => 'Apagado',              'color' => 'purple',  'orden' => 4],
            $base + ['clave' => 'otra_fecha',        'label' => 'Otra fecha',           'color' => 'teal',    'orden' => 5],
            ['clave' => 'volver_a_llamar',   'label' => 'Volver a llamar',      'color' => 'sky',     'orden' => 6,  'requiere_fecha' => true, 'requiere_hora' => true, 'requiere_sistema' => false, 'comentario_auto' => null, 'created_at' => $ahora, 'updated_at' => $ahora],
            $base + ['clave' => 'pago_pendiente',    'label' => 'Pago pendiente',       'color' => 'rose',    'orden' => 7],
            $base + ['clave' => 'por_confirmar',     'label' => 'Por confirmar',        'color' => 'amber',   'orden' => 8],
            $base + ['clave' => 'potencial',         'label' => 'Potencial',            'color' => 'violet',  'orden' => 9],
            ['clave' => 'reunion_pendiente', 'label' => 'Reunión pendiente',    'color' => 'yellow',  'orden' => 10, 'requiere_fecha' => true, 'requiere_hora' => true, 'requiere_sistema' => true, 'comentario_auto' => null, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['clave' => 'escribir_whatsapp', 'label' => 'Escribir al WhatsApp', 'color' => 'emerald', 'orden' => 11, 'requiere_fecha' => false, 'requiere_hora' => false, 'requiere_sistema' => false, 'comentario_auto' => $whatsappTexto, 'created_at' => $ahora, 'updated_at' => $ahora],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_response_options');
    }
};
