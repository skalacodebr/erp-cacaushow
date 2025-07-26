<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alterando o campo periodicidade para incluir 'semanal'
        DB::statement("ALTER TABLE contas_pagar MODIFY COLUMN periodicidade VARCHAR(255) CHECK (periodicidade IN ('semanal', 'mensal', 'bimestral', 'trimestral', 'semestral', 'anual'))");
        DB::statement("ALTER TABLE contas_receber MODIFY COLUMN periodicidade VARCHAR(255) CHECK (periodicidade IN ('semanal', 'mensal', 'bimestral', 'trimestral', 'semestral', 'anual'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter para o estado anterior (sem 'semanal')
        DB::statement("ALTER TABLE contas_pagar MODIFY COLUMN periodicidade VARCHAR(255) CHECK (periodicidade IN ('mensal', 'bimestral', 'trimestral', 'semestral', 'anual'))");
        DB::statement("ALTER TABLE contas_receber MODIFY COLUMN periodicidade VARCHAR(255) CHECK (periodicidade IN ('mensal', 'bimestral', 'trimestral', 'semestral', 'anual'))");
    }
};