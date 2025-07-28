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
        // Drop foreign key constraints first
        Schema::table('contas_receber', function (Blueprint $table) {
            if (Schema::hasColumn('contas_receber', 'cliente_id')) {
                $table->dropForeign(['cliente_id']);
            }
        });

        // Drop the existing table
        Schema::dropIfExists('clientes');
        
        // Recreate with correct structure
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            
            // Dados básicos
            $table->string('nome');
            $table->string('nome_fantasia')->nullable();
            $table->enum('tipo_pessoa', ['fisica', 'juridica'])->default('fisica');
            $table->string('cpf_cnpj')->unique()->nullable();
            $table->string('rg_ie')->nullable();
            
            // Contato
            $table->string('email')->nullable();
            $table->string('telefone')->nullable();
            $table->string('celular')->nullable();
            $table->string('website')->nullable();
            
            // Endereço
            $table->string('cep')->nullable();
            $table->string('endereco')->nullable();
            $table->string('numero')->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado', 2)->nullable();
            
            // Dados financeiros
            $table->decimal('limite_credito', 10, 2)->default(0);
            $table->integer('prazo_pagamento')->default(30); // dias
            $table->date('data_cadastro')->default(now());
            $table->date('data_ultima_compra')->nullable();
            
            // Controle
            $table->boolean('ativo')->default(true);
            $table->text('observacoes')->nullable();
            
            $table->timestamps();
            
            $table->index('nome');
            $table->index('cpf_cnpj');
            $table->index('tipo_pessoa');
            $table->index('ativo');
        });

        // Recreate foreign key
        Schema::table('contas_receber', function (Blueprint $table) {
            if (Schema::hasColumn('contas_receber', 'cliente_id')) {
                $table->foreign('cliente_id')->references('id')->on('clientes')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration recreates the table, so we can't reverse it
        // The down method would need to restore the previous incorrect structure
    }
};
