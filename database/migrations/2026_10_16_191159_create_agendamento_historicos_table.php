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
        Schema::create('agendamento_historicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agendamento_id')->constrained('agendamentos');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('acao');
            $table->json('alteracoes')->nullable();
            $table->timestamps();

            $table->index(['agendamento_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendamento_historicos');
    }
};
