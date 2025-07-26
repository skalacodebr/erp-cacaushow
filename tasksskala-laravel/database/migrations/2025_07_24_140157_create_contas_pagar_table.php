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
        Schema::create('contas_pagar', function (Blueprint $table) {
            $table->id();
            $table->string('descricao');
            $table->decimal('valor', 10, 2);
            $table->date('data_vencimento');
            $table->date('data_pagamento')->nullable();
            $table->foreignId('conta_bancaria_id')->nullable()->constrained('contas_bancarias');
            $table->string('tipo'); // fixa, parcelada, recorrente
            $table->integer('parcela_atual')->nullable();
            $table->integer('total_parcelas')->nullable();
            $table->string('periodicidade')->nullable(); // mensal, bimestral, trimestral, semestral, anual
            $table->date('data_fim_recorrencia')->nullable();
            $table->string('status')->default('pendente'); // pendente, pago, vencido, cancelado
            $table->string('categoria')->nullable();
            $table->string('fornecedor')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contas_pagar');
    }
};
