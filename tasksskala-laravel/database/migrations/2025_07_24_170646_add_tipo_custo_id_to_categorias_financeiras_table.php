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
        Schema::table('categorias_financeiras', function (Blueprint $table) {
            // Adicionar a nova coluna tipo_custo_id
            $table->foreignId('tipo_custo_id')->nullable()->after('tipo_custo')->constrained('tipos_custo')->onDelete('set null');
            
            // Remover a coluna tipo_custo antiga (depois de migrar os dados)
            // Esta parte será feita em uma migration separada após migrar os dados
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categorias_financeiras', function (Blueprint $table) {
            $table->dropForeign(['tipo_custo_id']);
            $table->dropColumn('tipo_custo_id');
        });
    }
};