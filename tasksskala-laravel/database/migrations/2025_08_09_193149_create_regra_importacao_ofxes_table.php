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
        Schema::create('regras_importacao_ofx', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->enum('tipo_conta', ['pagar', 'receber']);
            $table->string('campo_busca'); // beneficiario, descricao, numero_documento
            $table->string('padrao_busca'); // padrão regex ou texto para buscar
            $table->string('categoria_id')->nullable();
            $table->string('fornecedor_id')->nullable();
            $table->string('cliente_id')->nullable();
            $table->string('conta_bancaria_id')->nullable();
            $table->string('centro_custo_id')->nullable();
            $table->boolean('ativo')->default(true);
            $table->integer('prioridade')->default(0); // maior prioridade = aplicada primeiro
            $table->json('acoes_adicionais')->nullable(); // outras ações customizadas
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regras_importacao_ofx');
    }
};
