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
        Schema::create('plano_contas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique(); // Ex: 3.1.1.01
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('plano_contas')->onDelete('cascade');
            $table->integer('nivel')->default(1); // 1 = Grupo, 2 = Subgrupo, 3 = Conta, 4 = Subconta
            $table->enum('natureza', ['receita', 'despesa', 'resultado'])->default('despesa');
            $table->enum('tipo', ['sintetica', 'analitica'])->default('analitica'); // Sintética = agrupadora, Analítica = lançamentos
            $table->boolean('ativo')->default(true);
            $table->integer('ordem')->default(0); // Para ordenação customizada
            
            // Campos para DRE
            $table->boolean('dre_visivel')->default(true); // Se aparece no DRE
            $table->string('dre_formula')->nullable(); // Para contas calculadas (ex: "3.1 - 3.2")
            $table->enum('dre_tipo', [
                'receita_operacional',
                'deducao_receita',
                'custo',
                'despesa_operacional',
                'despesa_administrativa',
                'despesa_comercial',
                'despesa_financeira',
                'receita_financeira',
                'outras_receitas',
                'outras_despesas',
                'resultado'
            ])->nullable();
            
            $table->timestamps();
            
            $table->index('codigo');
            $table->index('parent_id');
            $table->index('natureza');
            $table->index('nivel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plano_contas');
    }
};