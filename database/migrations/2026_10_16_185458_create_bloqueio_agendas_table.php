<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bloqueio_agendas', function (Blueprint $table) {
            $table->id();
            $table->date('data')->index();
            $table->time('horario_inicio')->nullable();
            $table->time('horario_fim')->nullable();
            $table->string('motivo', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bloqueio_agendas');
    }
};
