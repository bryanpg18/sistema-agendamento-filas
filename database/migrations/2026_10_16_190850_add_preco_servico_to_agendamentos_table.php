<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->decimal('preco_servico', 10, 2)->nullable();
        });

        DB::table('agendamentos')->update([
            'preco_servico' => DB::raw('(select servicos.preco from servicos where servicos.id = agendamentos.servico_id)'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->dropColumn('preco_servico');
        });
    }
};
