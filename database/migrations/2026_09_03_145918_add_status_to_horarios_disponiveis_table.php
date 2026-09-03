<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('horarios_disponiveis', function (Blueprint $table) {
            $table->enum('status', ['disponivel', 'agendado', 'cancelado'])
                ->default('disponivel')
                ->after('horario');
        });
    }

    public function down(): void
    {
        Schema::table('horarios_disponiveis', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
