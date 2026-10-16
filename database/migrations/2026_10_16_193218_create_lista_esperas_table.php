<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lista_esperas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('servico_id')->constrained('servicos')->cascadeOnDelete();
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->time('horario')->nullable();
            $table->string('status')->default('aguardando');
            $table->timestamp('avisado_em')->nullable();
            $table->timestamps();

            $table->index(['status', 'servico_id', 'data_inicio', 'data_fim']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lista_esperas');
    }
};
